<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

// ─── GET single campaign ───
if (isset($_GET['action']) && $_GET['action'] === 'get') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'error' => 'ID nao informado']); exit; }
    $stmt = $conn->prepare("SELECT * FROM email_campaigns WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $r = $stmt->get_result();
    $camp = $r ? $r->fetch_assoc() : null;
    $stmt->close();
    echo json_encode(['success' => !!$camp, 'campaign' => $camp]);
    exit;
}

// ─── DELETE campaign ───
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID nao informado']);
        exit;
    }
    $stmt = $conn->prepare("SELECT tag FROM email_campaigns WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $r = $stmt->get_result();
    $tag = $r ? ($r->fetch_row()[0] ?? '') : '';
    $stmt->close();
    if ($tag) {
        $stmt2 = $conn->prepare("DELETE FROM email_tracking WHERE campaign_tag = ?");
        $stmt2->bind_param('s', $tag);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt3 = $conn->prepare("DELETE FROM email_campaigns WHERE id = ?");
    $stmt3->bind_param('i', $id);
    $stmt3->execute();
    $stmt3->close();
    echo json_encode(['success' => true, 'message' => 'Campanha e eventos excluidos']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['tag'])) {
    echo json_encode(['success' => false, 'error' => 'Tag nao informada']);
    exit;
}

$tag = $input['tag'];
$subject = $input['subject'] ?? '';
$template = $input['template'] ?? '';
$segments = $input['segments'] ?? '';
$totalRecipients = intval($input['total_recipients'] ?? 0);
$sentCount = intval($input['sent_count'] ?? 0);

$stmt = $conn->prepare("INSERT INTO email_campaigns (tag, subject, template, segments, total_recipients, sent_count) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE subject = VALUES(subject), template = VALUES(template), segments = VALUES(segments), total_recipients = VALUES(total_recipients), sent_count = VALUES(sent_count)");
$stmt->bind_param('ssssii', $tag, $subject, $template, $segments, $totalRecipients, $sentCount);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => true]);

