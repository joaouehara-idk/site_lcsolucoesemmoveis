<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$configPath = __DIR__ . '/../email_brevo_api.json';
$config = [];
if (file_exists($configPath)) {
    $config = json_decode(file_get_contents($configPath), true) ?: [];
}

$apiKey = $config['api_key'] ?? '';
$action = $_GET['action'] ?? 'status';

/**
 * Call Brevo API v3
 */
function brevoApi($endpoint, $apiKey) {
    $url = "https://api.brevo.com/v3/$endpoint";
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                "api-key: $apiKey",
                "Accept: application/json",
            ],
            'timeout' => 30,
        ]
    ]);
    $resp = file_get_contents($url, false, $ctx);
    if ($resp === false) {
        $err = error_get_last();
        return ['_error' => $err['message'] ?? 'HTTP request failed'];
    }
    $decoded = json_decode($resp, true);
    return $decoded !== null ? $decoded : ['_error' => 'Invalid JSON response'];
}

/**
 * Map Brevo event type to CRM event type
 */
function mapEvent($brevoEvent) {
    $map = [
        'delivered' => 'delivered',
        'opened' => 'open',
        'clicked' => 'click',
        'hardBounce' => 'bounce',
        'softBounce' => 'bounce',
        'blocked' => 'blocked',
        'spam' => 'spam',
        'unsubscribed' => 'unsubscribed',
        'invalid_email' => 'bounce',
        'deferred' => 'deferred',
        'error' => 'error',
    ];
    return $map[$brevoEvent] ?? $brevoEvent;
}

// ─── Status ───
if ($action === 'status') {
    $connected = false;
    $accountName = '';
    $emailCount = 0;
    $remaining = 0;

    if ($apiKey) {
        $info = brevoApi('account', $apiKey);
        if ($info && isset($info['email'])) {
            $connected = true;
            $accountName = $info['company_name'] ?? $info['email'] ?? '';
        }
    }

    echo json_encode([
        'success' => true,
        'configured' => !empty($apiKey),
        'connected' => $connected,
        'account_name' => $accountName,
    ]);
    exit;
}

// ─── Save config ───
if ($action === 'save_config') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Metodo nao permitido']);
        exit;
    }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'JSON invalido']);
        exit;
    }

    $config['api_key'] = trim($input['api_key'] ?? '');
    file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true, 'message' => 'Chave API salva']);
    exit;
}

// ─── Sync ───
if ($action === 'sync') {
    if (!$apiKey) {
        echo json_encode(['success' => false, 'error' => 'API key nao configurada']);
        exit;
    }

    $days = max(1, min(90, intval($_GET['days'] ?? 7)));
    $limit = min(5000, max(1, intval($_GET['limit'] ?? 100)));
    $offset = max(0, intval($_GET['offset'] ?? 0));
    $tagFilter = $_GET['tag'] ?? '';

    require_once __DIR__ . '/../includes/db_config.php';
    $conn = getDbConnection();
    $conn->set_charset('utf8mb4');

    $startDate = date('Y-m-d', strtotime("-{$days} days"));
    $endDate = date('Y-m-d');

    $endpoint = "smtp/statistics/events?limit=$limit&offset=$offset&startDate=$startDate&endDate=$endDate";
    if ($tagFilter) {
        $endpoint .= '&tags=' . urlencode($tagFilter);
    }

    $data = brevoApi($endpoint, $apiKey);
    if (isset($data['_error'])) {
        echo json_encode(['success' => false, 'error' => 'Falha HTTP ao conectar na API Brevo', 'detail' => $data['_error']]);
        exit;
    }
    if (isset($data['message'])) {
        echo json_encode(['success' => false, 'error' => 'Brevo: ' . $data['message'], 'code' => $data['code'] ?? '']);
        exit;
    }
    if (!isset($data['events'])) {
        echo json_encode(['success' => false, 'error' => 'Resposta inesperada da API', 'data' => $data]);
        exit;
    }

    // Deleta registros antigos e reimporta
    $startDt = $startDate . ' 00:00:00';
    $endDt = $endDate . ' 23:59:59';

    $conn->begin_transaction();

    $stmtDel = $conn->prepare("DELETE FROM email_tracking WHERE ts >= ? AND ts <= ?");
    $stmtDel->bind_param('ss', $startDt, $endDt);
    $stmtDel->execute();
    $deleted = $stmtDel->affected_rows;
    $stmtDel->close();

    $imported = 0;
    $stmt = $conn->prepare("INSERT IGNORE INTO email_tracking (event_type, email, campaign_tag, subject, link_url, message_id, ts, raw_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssss', $eventType, $email, $tag, $subject, $link, $msgId, $ts, $rawJson);

    foreach ($data['events'] as $ev) {
        $eventType = mapEvent($ev['event'] ?? '');
        $email = strtolower(trim($ev['email'] ?? ''));
        $tag = $ev['tag'] ?? '';
        $subject = $ev['subject'] ?? '';

        $link = '';
        if ($eventType === 'click' && isset($ev['link'])) {
            $link = $ev['link'];
        }

        $msgId = $ev['message-id'] ?? '';
        $ts = date('Y-m-d H:i:s', strtotime($ev['date'] ?? 'now'));
        $rawJson = json_encode($ev);

        if (!$email) continue;

        $stmt->execute();
        if ($stmt->affected_rows > 0) $imported++;
    }

    $stmt->close();
    $conn->commit();

    // Atualiza sent_count das campanhas
    $conn->query("
        UPDATE email_campaigns c
        JOIN (
            SELECT campaign_tag, COUNT(*) as delivered
            FROM email_tracking
            WHERE event_type = 'delivered' AND campaign_tag != ''
            GROUP BY campaign_tag
        ) t ON c.tag = t.campaign_tag
        SET c.sent_count = t.delivered
    ");

    $conn->close();

    echo json_encode([
        'success' => true,
        'deleted' => $deleted,
        'imported' => $imported,
        'total_api' => count($data['events']),
        'message' => "{$deleted} registros antigos removidos, {$imported} importados da Brevo de " . count($data['events']) . " retornados",
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acao invalida']);

