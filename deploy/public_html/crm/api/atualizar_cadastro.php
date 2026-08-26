<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$segments = [
    'arquitetos_ms'           => 'Arquitetos',
    'designers_interiores_ms' => 'Designers de Interiores',
    'designers_ms'            => 'Designers',
    'construtoras_ms'         => 'Construtoras',
    'imobiliarias_ms'         => 'Imobiliárias',
    'engenheiros_ms'          => 'Engenheiros',
    'lojas_marcenarias_ms'    => 'Lojas e Marcenarias',
    'paisagismo_decoracao_ms' => 'Paisagismo e Decoração',
];

$action = $_GET['action'] ?? 'info';

// ─── INFO: diagnostico do segmento ───
if ($action === 'info') {
    $info = [];
    foreach ($segments as $seg => $label) {
        $r = $conn->query("
            SELECT (SELECT COUNT(*) FROM `$seg`) as total,
                   (SELECT COUNT(*) FROM `$seg` WHERE razao_social IS NULL OR razao_social = '') as sem_razao,
                   (SELECT COUNT(*) FROM `$seg` WHERE telefone IS NULL OR telefone = '') as sem_tel,
                   (SELECT COUNT(*) FROM `$seg` WHERE correio_eletronico IS NULL OR correio_eletronico = '') as sem_email,
                   (SELECT COUNT(*) FROM `$seg` WHERE capital_social IS NULL OR capital_social = '' OR capital_social = '0,00') as sem_capital,
                   (SELECT COUNT(DISTINCT s2.cnpj_basico) FROM `$seg` s2 JOIN estabelecimentos_ms e2 ON e2.cnpj_basico = s2.cnpj_basico WHERE e2.cnpj_ordem IS NOT NULL) as com_cnpj
        ");
        $info[$seg] = $r->fetch_assoc();
        $info[$seg]['label'] = $label;
    }
    echo json_encode(['success' => true, 'total_segments' => count($segments), 'segments' => $info]);
    exit;
}

// ─── UPDATE: consulta BrasilAPI e atualiza lote ───
if ($action === 'update') {
    $seg = $_GET['seg'] ?? '';
    $limit = min(20, max(1, intval($_GET['limit'] ?? 5)));
    $offset = max(0, intval($_GET['offset'] ?? 0));

    if (!isset($segments[$seg])) {
        echo json_encode(['success' => false, 'error' => 'Segmento invalido']);
        exit;
    }

    // Usa DISTINCT para evitar processar o mesmo cnpj_basico multiplas vezes
    $rows = $conn->query("
        SELECT DISTINCT s.cnpj_basico,
               (SELECT CONCAT(e.cnpj_ordem, e.cnpj_dv) FROM estabelecimentos_ms e WHERE e.cnpj_basico = s.cnpj_basico AND e.cnpj_ordem IS NOT NULL LIMIT 1) as cnpj_sufixo,
               s.razao_social, s.situacao_cadastral, s.capital_social,
               s.logradouro, s.bairro, s.cep, s.nome_municipio, s.uf,
               s.ddd, s.telefone, s.correio_eletronico
        FROM `$seg` s
        HAVING cnpj_sufixo IS NOT NULL
        ORDER BY s.cnpj_basico
        LIMIT $limit OFFSET $offset
    ");

    if (!$rows || !$rows->num_rows) {
        echo json_encode(['success' => false, 'error' => 'Nenhum registro encontrado', 'offset' => $offset]);
        exit;
    }

    $records = $rows->fetch_all(MYSQLI_ASSOC);
    $updated = 0;
    $errors = [];
    $allChanges = [];

    foreach ($records as $row) {
        $cnpj = $row['cnpj_basico'] . $row['cnpj_sufixo'];
        if (strlen($cnpj) !== 14) {
            $errors[] = "CNPJ invalido: {$row['cnpj_basico']}";
            continue;
        }

        $ch = curl_init("https://brasilapi.com.br/api/cnpj/v1/$cnpj");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'CRM-LC/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $httpCode !== 200) {
            $errors[] = "Falha consulta {$row['cnpj_basico']} (HTTP $httpCode)";
            usleep(4000000);
            continue;
        }

        $api = json_decode($resp, true);
        if (!$api || isset($api['message'])) {
            $errors[] = "API error {$row['cnpj_basico']}: " . ($api['message'] ?? 'resposta invalida');
            usleep(4000000);
            continue;
        }

        $updates = [];
        $changes = [];

        // ─── Mapeamento baseado no OpenAPI oficial ───
        // Campos diretos
        $direct = [
            'razao_social'         => $api['razao_social'] ?? '',
            'nome_fantasia'        => $api['nome_fantasia'] ?? '',
            'logradouro'           => $api['logradouro'] ?? '',
            'numero'               => $api['numero'] ?? '',
            'bairro'               => $api['bairro'] ?? '',
            'nome_municipio'       => $api['municipio'] ?? '',
            'uf'                   => $api['uf'] ?? '',
            'correio_eletronico'   => $api['email'] ?? '',
            'natureza_juridica'    => $api['natureza_juridica'] ?? '',
        ];

        // CEP: integer → string 8-digit com leading zeros
        if (isset($api['cep']) && $api['cep'] !== null) {
            $direct['cep'] = str_pad((string)$api['cep'], 8, '0', STR_PAD_LEFT);
        }

        // Capital social: integer → BR format "10000,00"
        if (isset($api['capital_social']) && $api['capital_social'] !== null) {
            $direct['capital_social'] = number_format((float)$api['capital_social'], 2, ',', '');
        }

        // Situacao cadastral: integer 1-8 → string "02"
        if (isset($api['situacao_cadastral']) && $api['situacao_cadastral'] !== null) {
            $direct['situacao_cadastral'] = str_pad((string)(int)$api['situacao_cadastral'], 2, '0', STR_PAD_LEFT);
        }

        // Telefone: ddd_telefone_1 = "6733221234" → DDD "67" + telefone "33221234"
        if (!empty($api['ddd_telefone_1'])) {
            $telRaw = preg_replace('/\D/', '', $api['ddd_telefone_1']);
            if (strlen($telRaw) >= 10) {
                $direct['ddd'] = substr($telRaw, 0, 2);
                $direct['telefone'] = substr($telRaw, 2);
            }
        }

        // CNAE fiscal principal
        if (isset($api['cnae_fiscal']) && $api['cnae_fiscal'] !== null) {
            $direct['cnae_fiscal_principal'] = (string)$api['cnae_fiscal'];
        }

        // Data inicio atividade
        if (!empty($api['data_inicio_atividade'])) {
            $direct['data_inicio_atividade'] = preg_replace('/\D/', '', $api['data_inicio_atividade']);
        }

        // Porte
        if (isset($api['codigo_porte']) && $api['codigo_porte'] !== null) {
            $direct['porte'] = (string)$api['codigo_porte'];
        }

        foreach ($direct as $col => $new) {
            $old = trim($row[$col] ?? '');
            $new = trim($new);
            if ($new !== '' && $new !== $old) {
                $esc = $conn->real_escape_string($new);
                $updates[] = "`$col` = '$esc'";
                $changes[] = "$col: " . mb_substr($old, 0, 40) . " → " . mb_substr($new, 0, 40);
            }
        }

        if ($updates) {
            $sql = "UPDATE `$seg` SET " . implode(', ', $updates) . " WHERE cnpj_basico = '{$row['cnpj_basico']}'";
            $conn->query($sql);
            $updated++;
            $allChanges[] = [
                'cnpj' => $row['cnpj_basico'],
                'razao' => $row['razao_social'] ?: $api['razao_social'] ?? '',
                'fields' => $changes,
            ];
        }

        usleep(350000);
    }

    echo json_encode([
        'success' => true,
        'segment' => $seg,
        'segment_label' => $segments[$seg],
        'processed' => count($records),
        'updated' => $updated,
        'errors' => count($errors),
        'error_details' => array_slice($errors, 0, 10),
        'changes' => $allChanges,
        'next_offset' => $offset + $limit,
    ]);
    exit;
}

// ─── BATCH: configura atualizacao completa ───
if ($action === 'batch') {
    $seg = $_GET['seg'] ?? 'todas';
    $limit = min(20, max(1, intval($_GET['limit'] ?? 10)));

    if ($seg === 'todas') {
        $result = [];
        $totalGeral = 0;
        foreach ($segments as $table => $label) {
            $r = $conn->query("SELECT COUNT(DISTINCT s.cnpj_basico) FROM `$table` s JOIN estabelecimentos_ms e ON e.cnpj_basico = s.cnpj_basico WHERE e.cnpj_ordem IS NOT NULL");
            $total = $r ? (int)$r->fetch_row()[0] : 0;
            $r2 = $conn->query("SELECT COUNT(*) FROM `$table`");
            $totalReg = $r2 ? (int)$r2->fetch_row()[0] : 0;
            $totalGeral += $total;
            $result[] = ['table' => $table, 'label' => $label, 'total' => $total];
        }

        $reqsTotal = ceil($totalGeral / $limit);
        echo json_encode([
            'success' => true,
            'segment' => 'todas',
            'segments' => $result,
            'total_geral' => $totalGeral,
            'batch_size' => $limit,
            'estimated_requests' => $reqsTotal,
            'estimated_time_min' => round($totalGeral * 0.35 / 60, 1),
            'instructions' => "Use ?action=update&seg=NOME&limit=$limit&offset=0, depoise increment offset ate fim de cada segmento."
        ]);
    } else {
        if (!isset($segments[$seg])) {
            echo json_encode(['success' => false, 'error' => 'Segmento invalido']);
            exit;
        }
        $totalR = $conn->query("SELECT COUNT(DISTINCT s.cnpj_basico) FROM `$seg` s JOIN estabelecimentos_ms e ON e.cnpj_basico = s.cnpj_basico WHERE e.cnpj_ordem IS NOT NULL");
        $total = $totalR ? (int)$totalR->fetch_row()[0] : 0;
        echo json_encode([
            'success' => true,
            'segment' => $seg,
            'label' => $segments[$seg],
            'total' => (int)$total,
            'batch_size' => $limit,
            'estimated_requests' => ceil($total / $limit),
            'estimated_time_min' => round($total * 0.35 / 60, 1),
            'instructions' => "Chame ?action=update&seg=$seg&limit=$limit&offset=N (N = 0, $limit, " . ($limit * 2) . ", ...) ate 'Nenhum registro'"
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acao invalida. Use: info, update, batch']);

