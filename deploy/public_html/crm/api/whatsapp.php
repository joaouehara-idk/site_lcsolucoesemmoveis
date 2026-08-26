<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../whatsapp_config.php';

$action = $_GET['action'] ?? '';

// ─── Enviar mensagem via WhatsApp Cloud API ───
if ($action === 'send') {
    if (WHATSAPP_MODE !== 'api') {
        echo json_encode(['success' => false, 'error' => 'WhatsApp API nao configurada. Use mode walink.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $to = preg_replace('/\D/', '', $input['to'] ?? '');
    $message = trim($input['message'] ?? '');

    if (!$to || !$message) {
        echo json_encode(['success' => false, 'error' => 'Destinatario e mensagem obrigatorios.']);
        exit;
    }

    if (strlen($to) < 10) $to = '55' . $to;

    $url = "https://graph.facebook.com/" . WHATSAPP_API_VERSION . "/" . WHATSAPP_PHONE_ID . "/messages";
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => ['body' => $message],
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . WHATSAPP_TOKEN,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($resp, true);

    if ($httpCode === 200 || $httpCode === 201) {
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error']['message'] ?? 'Falha no envio']);
    }
    exit;
}

// ─── Gerar link wa.me ───
if ($action === 'link') {
    $tel = preg_replace('/\D/', '', $_GET['tel'] ?? '');
    $msg = $_GET['msg'] ?? '';

    if (!$tel) {
        echo json_encode(['success' => false, 'error' => 'Telefone obrigatorio.']);
        exit;
    }

    if (strlen($tel) < 10) $tel = '55' . $tel;

    $url = "https://wa.me/$tel";
    if ($msg) $url .= "?text=" . urlencode($msg);

    echo json_encode(['success' => true, 'url' => $url]);
    exit;
}

// ─── Status / Info ───
if ($action === 'status') {
    echo json_encode([
        'success' => true,
        'mode' => WHATSAPP_MODE,
        'api_configured' => WHATSAPP_MODE === 'api' && !empty(WHATSAPP_TOKEN),
        'company_number' => WHATSAPP_COMPANY_NUMBER,
    ]);
    exit;
}

// ─── Listar registros de um segmento com WhatsApp ───
if ($action === 'segment_list') {
    $seg = $_GET['seg'] ?? '';
    $q = $_GET['q'] ?? '';
    $segments = [
        'arquitetos_ms', 'designers_interiores_ms', 'designers_ms',
        'construtoras_ms', 'imobiliarias_ms', 'engenheiros_ms',
        'lojas_marcenarias_ms', 'paisagismo_decoracao_ms',
    ];

    if (!in_array($seg, $segments)) {
        echo json_encode(['success' => false, 'error' => 'Segmento invalido']);
        exit;
    }

    $where = "s.telefone IS NOT NULL AND s.telefone != ''";
    if ($q) {
        $qEsc = $conn->real_escape_string($q);
        $where .= " AND (s.razao_social LIKE '%$qEsc%' OR s.nome_fantasia LIKE '%$qEsc%' OR s.bairro LIKE '%$qEsc%' OR CONCAT(s.ddd, s.telefone) LIKE '%$qEsc%')";
    }

    $r = $conn->query("SELECT s.cnpj_basico, s.razao_social, s.nome_fantasia, s.ddd, s.telefone, s.bairro, s.nome_municipio FROM `$seg` s WHERE $where ORDER BY s.razao_social ASC");
    $records = [];
    while ($row = $r->fetch_assoc()) {
        $tel = preg_replace('/\D/', '', ($row['ddd'] ?? '') . ($row['telefone'] ?? ''));
        if (strlen($tel) >= 10) {
            $numSemDDD = $row['telefone'] ?? ''; // raw number without DDD
            $precisa9 = (strlen($tel) === 10) && (strlen($numSemDDD) === 8) && preg_match('/^[6-9]/', $numSemDDD);
            $waLink = 'https://wa.me/55' . $tel;
            $waLink9 = null;
            if ($precisa9) {
                $telCom9 = ($row['ddd'] ?? '') . '9' . $numSemDDD;
                $telCom9 = preg_replace('/\D/', '', $telCom9);
                $waLink9 = 'https://wa.me/55' . $telCom9;
            }
            $records[] = [
                'cnpj' => $row['cnpj_basico'],
                'nome' => $row['razao_social'] ?: $row['nome_fantasia'] ?: 'N/I',
                'fantasia' => $row['nome_fantasia'] ?? '',
                'telefone' => $tel,
                'telefone_formatado' => '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, (strlen($tel) === 11 ? 5 : 4)) . '-' . substr($tel, -4),
                'bairro' => $row['bairro'] ?? '',
                'cidade' => $row['nome_municipio'] ?? '',
                'wa_link' => $waLink,
                'wa_link_9' => $waLink9,
                'precisa_9' => $precisa9,
            ];
        }
    }

    echo json_encode(['success' => true, 'segment' => $seg, 'total' => count($records), 'records' => $records]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acao invalida. Use: send, link, status, segment_list']);
