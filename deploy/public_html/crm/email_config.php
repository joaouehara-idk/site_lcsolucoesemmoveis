<?php
$serverUrl = 'http://127.0.0.1:5001';

$action = $_GET['action'] ?? '';

if ($action === 'get_config') {
    $resp = @file_get_contents("$serverUrl/config");
    if (!$resp) { echo json_encode(['success' => false, 'error' => 'Servidor offline']); exit; }
    echo $resp; exit;
}

if ($action === 'save_config') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'error' => 'Metodo nao permitido']); exit; }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['success' => false, 'error' => 'JSON invalido']); exit; }
    $payload = json_encode($input);
    $ctx = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\nContent-Length: " . strlen($payload) . "\r\n",
        'content' => $payload, 'timeout' => 10,
    ]]);
    $resp = @file_get_contents("$serverUrl/config", false, $ctx);
    if (!$resp) { echo json_encode(['success' => false, 'error' => 'Servidor offline']); exit; }
    echo $resp; exit;
}

if ($action === 'test_config') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'error' => 'Metodo nao permitido']); exit; }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['success' => false, 'error' => 'JSON invalido']); exit; }
    $payload = json_encode($input);
    $ctx = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\nContent-Length: " . strlen($payload) . "\r\n",
        'content' => $payload, 'timeout' => 15,
    ]]);
    $resp = @file_get_contents("$serverUrl/test", false, $ctx);
    if (!$resp) { echo json_encode(['success' => false, 'error' => 'Servidor offline']); exit; }
    echo $resp; exit;
}

return [
    'email_server_url' => $serverUrl,
    'from_name' => 'LC Soluções em Móveis',
    'from_email' => 'lcmovel.planejadocg@gmail.com',
];
