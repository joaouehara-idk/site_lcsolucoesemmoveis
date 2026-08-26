<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$config = include __DIR__ . '/../email_config.php';
$serverUrl = $config['email_server_url'];

$action = $_GET['action'] ?? '';

if ($action === 'templates') {
    $templatesDir = __DIR__ . '/../email_templates';
    $templates = [];
    $labelMap = [
        'boas_vindas.html' => 'Boas-Vindas',
        'novidades.html' => 'Novidades',
        'promocoes.html' => 'Promocoes',
        'ofertas.html' => 'Ofertas',
        'parceiros.html' => 'Parceiros',
    ];
    if (is_dir($templatesDir)) {
        $files = scandir($templatesDir);
        sort($files);
        foreach ($files as $f) {
            if (str_ends_with($f, '.html')) {
                $templates[] = [
                    'file' => $f,
                    'label' => $labelMap[$f] ?? ucwords(str_replace(['.html','_'], ['', ' '], $f)),
                ];
            }
        }
    }
    echo json_encode(['templates' => $templates]);
    exit;
}

if ($action === 'get') {
    $file = basename($_GET['file'] ?? '');
    $path = __DIR__ . '/../email_templates/' . $file;
    if (!$file || !file_exists($path)) {
        echo json_encode(['success' => false, 'error' => 'Template nao encontrado']);
        exit;
    }
    header('Content-Type: text/html; charset=utf-8');
    readfile($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

if ($action === 'send' || $action === 'test') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        exit;
    }

    $payload = json_encode($input);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nContent-Length: " . strlen($payload) . "\r\n",
            'content' => $payload,
            'timeout' => 300,
        ]
    ]);

    $resp = file_get_contents("$serverUrl/send", false, $ctx);
    if ($resp === false) {
        $err = error_get_last();
        echo json_encode(['success' => false, 'error' => 'Servidor de email offline', 'detail' => $err['message'] ?? '']);
        exit;
    }
    echo $resp;
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação inválida']);
