<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$action = $_GET['action'] ?? 'overview';

if ($action === 'overview') {
    $r = $conn->query("SELECT
        COUNT(DISTINCT email) as total_contacts,
        COUNT(*) as total_events,
        SUM(event_type = 'delivered') as delivered,
        SUM(event_type = 'open') as opens,
        SUM(event_type = 'click') as clicks,
        SUM(event_type IN ('bounce','hardBounce','softBounce')) as bounces,
        SUM(event_type = 'blocked') as blocked,
        SUM(event_type = 'spam') as spam,
        SUM(event_type = 'unsubscribed') as unsubscribes
    FROM email_tracking");
    echo json_encode(['success' => true, 'data' => $r->fetch_assoc()]);
    exit;
}

if ($action === 'campaigns') {
    $r = $conn->query("SELECT
        c.*,
        COALESCE(t.delivered,0) as delivered,
        COALESCE(t.opens,0) as opens,
        COALESCE(t.clicks,0) as clicks,
        COALESCE(t.bounces,0) as bounces,
        ROUND(COALESCE(t.opens,0) * 100.0 / NULLIF(c.sent_count,0), 1) as open_rate,
        ROUND(COALESCE(t.clicks,0) * 100.0 / NULLIF(c.sent_count,0), 1) as click_rate
    FROM email_campaigns c
    LEFT JOIN (
        SELECT campaign_tag,
            SUM(event_type='delivered') as delivered,
            SUM(event_type='open') as opens,
            SUM(event_type='click') as clicks,
            SUM(event_type IN ('bounce','hardBounce','softBounce')) as bounces
        FROM email_tracking GROUP BY campaign_tag
    ) t ON t.campaign_tag = c.tag
    ORDER BY c.created_at DESC");
    $campaigns = $r->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['success' => true, 'campaigns' => $campaigns]);
    exit;
}

if ($action === 'events') {
    $limit = min(200, max(1, intval($_GET['limit'] ?? 50)));
    $offset = max(0, intval($_GET['offset'] ?? 0));
    $eventFilter = $_GET['event'] ?? '';
    $tagFilter = $_GET['tag'] ?? '';

    $where = [];
    $params = [];
    $types = '';

    if ($eventFilter) {
        $where[] = 'event_type = ?';
        $params[] = $eventFilter;
        $types .= 's';
    }
    if ($tagFilter) {
        $where[] = 'campaign_tag = ?';
        $params[] = $tagFilter;
        $types .= 's';
    }

    $wh = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $totalR = $conn->query("SELECT COUNT(*) as c FROM email_tracking $wh" . ($params ? '' : ''));
    if ($params) {
        $st = $conn->prepare("SELECT COUNT(*) as c FROM email_tracking $wh");
        $st->bind_param($types, ...$params);
        $st->execute();
        $totalR = $st->get_result();
    }
    $total = $totalR->fetch_assoc()['c'];

    $q = "SELECT * FROM email_tracking $wh ORDER BY ts DESC LIMIT $limit OFFSET $offset";
    if ($params) {
        $st = $conn->prepare($q);
        $st->bind_param($types, ...$params);
        $st->execute();
        $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $rows = $conn->query($q)->fetch_all(MYSQLI_ASSOC);
    }

    echo json_encode(['success' => true, 'total' => $total, 'events' => $rows]);
    exit;
}

if ($action === 'daily') {
    $days = min(90, max(1, intval($_GET['days'] ?? 30)));
    $r = $conn->query("SELECT
        DATE(ts) as date,
        SUM(event_type='delivered') as delivered,
        SUM(event_type='open') as opens,
        SUM(event_type='click') as clicks,
        SUM(event_type IN ('bounce','hardBounce','softBounce')) as bounces
    FROM email_tracking
    WHERE ts >= DATE_SUB(NOW(), INTERVAL $days DAY)
    GROUP BY DATE(ts) ORDER BY date ASC");
    $rows = $r->fetch_all(MYSQLI_ASSOC);

    // Preenche todos os dias (inclusive zeros)
    $map = [];
    foreach ($rows as $row) {
        $map[$row['date']] = $row;
    }
    $daily = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        if (isset($map[$date])) {
            $daily[] = $map[$date];
        } else {
            $daily[] = ['date' => $date, 'delivered' => '0', 'opens' => '0', 'clicks' => '0', 'bounces' => '0'];
        }
    }

    echo json_encode(['success' => true, 'daily' => $daily]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acao invalida']);

