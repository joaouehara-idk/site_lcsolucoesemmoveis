<?php
require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

// Simple token auth (obrigatório se configurado)
$configPath = __DIR__ . '/../email_brevo_api.json';
$config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
$webhookToken = $config['webhook_token'] ?? '';
$reqToken = $_SERVER['HTTP_X_WEBHOOK_TOKEN'] ?? '';
if (!$webhookToken || $reqToken !== $webhookToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Nao autorizado']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['event'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Evento invalido']);
    exit;
}

$event = $data['event'];
$email = strtolower(trim($data['email'] ?? ''));
$campaignTag = $data['tag'] ?? null;
$subject = $data['subject'] ?? null;
$linkUrl = $data['link'] ?? null;
$messageId = $data['message-id'] ?? null;
$ts = $data['date'] ?? date('Y-m-d H:i:s');

$ts = date('Y-m-d H:i:s', strtotime($ts));

$stmt = $conn->prepare("INSERT INTO email_tracking (event_type, email, campaign_tag, subject, link_url, message_id, ts, raw_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssssss', $event, $email, $campaignTag, $subject, $linkUrl, $messageId, $ts, $input);
$stmt->execute();
$stmt->close();

if ($event === 'delivered' && $campaignTag) {
    $stmt = $conn->prepare("UPDATE email_campaigns SET sent_count = sent_count + 1 WHERE tag = ?");
    $stmt->bind_param('s', $campaignTag);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo json_encode(['success' => true]);

