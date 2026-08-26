<?php
/**
 * Atualizacao cadastral completa via BrasilAPI.
 * Uso: php api/atualizar_tudo.php [--continue]
 *
 * --continue: retoma de onde parou (le checkpoint.json)
 *
 * Para interromper: Ctrl+C (checkpoint salvo na proxima iteracao)
 */

$start = microtime(true);

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

$checkpointFile = __DIR__ . '/checkpoint.json';

// Carrega checkpoint se --continue
$checkpoint = ['seg' => null, 'offset' => 0];
if (in_array('--continue', $argv ?? [])) {
    if (file_exists($checkpointFile)) {
        $checkpoint = json_decode(file_get_contents($checkpointFile), true);
        echo "Retomando de checkpoint: seg={$checkpoint['seg']}, offset={$checkpoint['offset']}\n";
    } else {
        echo "Nenhum checkpoint encontrado, comecando do inicio.\n";
    }
}

$totalGeral = 0;
$updatedGeral = 0;
$errorsGeral = 0;
$skipSegments = $checkpoint['seg'] !== null;

function saveCheckpoint($seg, $offset) {
    global $checkpointFile;
    file_put_contents($checkpointFile, json_encode(['seg' => $seg, 'offset' => $offset, 'time' => date('H:i:s')]));
}

foreach ($segments as $seg => $label) {
    // Pula segmentos anteriores ao checkpoint
    if ($skipSegments) {
        if ($seg === $checkpoint['seg']) $skipSegments = false;
        else { echo " Pulando $label ($seg) - ja processado\n"; continue; }
    }

    echo "\n=== $label ($seg) ===\n";

    $r = $conn->query("SELECT COUNT(DISTINCT s.cnpj_basico)
        FROM `$seg` s JOIN estabelecimentos_ms e ON e.cnpj_basico = s.cnpj_basico WHERE e.cnpj_ordem IS NOT NULL");
    $total = (int)$r->fetch_row()[0];
    echo "  CNPJs: $total\n";

    // Busca com offset via checkpoint
    $offset = ($seg === $checkpoint['seg']) ? $checkpoint['offset'] : 0;

    $batchSize = 100;
    $updated = 0;
    $errors = 0;

    while ($offset < $total) {
        $rows = $conn->query("
            SELECT DISTINCT s.cnpj_basico,
                   (SELECT CONCAT(e.cnpj_ordem, e.cnpj_dv) FROM estabelecimentos_ms e
                    WHERE e.cnpj_basico = s.cnpj_basico AND e.cnpj_ordem IS NOT NULL LIMIT 1) as cnpj_sufixo
            FROM `$seg` s
            HAVING cnpj_sufixo IS NOT NULL
            ORDER BY s.cnpj_basico
            LIMIT $batchSize OFFSET $offset
        ");

        if (!$rows || !$rows->num_rows) break;

        while ($row = $rows->fetch_assoc()) {
            $cnpj = $row['cnpj_basico'] . $row['cnpj_sufixo'];
            if (strlen($cnpj) !== 14) { $errors++; continue; }

            $ch = curl_init("https://brasilapi.com.br/api/cnpj/v1/$cnpj");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20,
                CURLOPT_USERAGENT => 'CRM-LC/1.0', CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $resp = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($resp === false || $httpCode !== 200) { $errors++; usleep(4000000); continue; }

            $api = json_decode($resp, true);
            if (!$api || isset($api['message'])) { $errors++; usleep(4000000); continue; }

            $updates = [];

            $direct = [
                'razao_social'       => $api['razao_social'] ?? '',
                'nome_fantasia'      => $api['nome_fantasia'] ?? '',
                'logradouro'         => $api['logradouro'] ?? '',
                'numero'             => $api['numero'] ?? '',
                'bairro'             => $api['bairro'] ?? '',
                'nome_municipio'     => $api['municipio'] ?? '',
                'uf'                 => $api['uf'] ?? '',
                'correio_eletronico' => $api['email'] ?? '',
                'natureza_juridica'  => $api['natureza_juridica'] ?? '',
            ];

            if (isset($api['cep']) && $api['cep'] !== null)
                $direct['cep'] = str_pad((string)$api['cep'], 8, '0', STR_PAD_LEFT);
            if (isset($api['capital_social']) && $api['capital_social'] !== null)
                $direct['capital_social'] = number_format((float)$api['capital_social'], 2, ',', '');
            if (isset($api['situacao_cadastral']) && $api['situacao_cadastral'] !== null)
                $direct['situacao_cadastral'] = str_pad((string)(int)$api['situacao_cadastral'], 2, '0', STR_PAD_LEFT);
            if (!empty($api['ddd_telefone_1'])) {
                $telRaw = preg_replace('/\D/', '', $api['ddd_telefone_1']);
                if (strlen($telRaw) >= 10) {
                    $direct['ddd'] = substr($telRaw, 0, 2);
                    $direct['telefone'] = substr($telRaw, 2);
                }
            }
            if (isset($api['cnae_fiscal']) && $api['cnae_fiscal'] !== null)
                $direct['cnae_fiscal_principal'] = (string)$api['cnae_fiscal'];
            if (!empty($api['data_inicio_atividade']))
                $direct['data_inicio_atividade'] = preg_replace('/\D/', '', $api['data_inicio_atividade']);
            if (isset($api['codigo_porte']) && $api['codigo_porte'] !== null)
                $direct['porte'] = (string)$api['codigo_porte'];

            foreach ($direct as $col => $new) {
                $new = trim($new);
                if ($new === '') continue;
                $updates[] = "`$col` = '" . $conn->real_escape_string($new) . "'";
            }

            if ($updates) {
                $conn->query("UPDATE `$seg` SET " . implode(', ', $updates) . " WHERE cnpj_basico = '{$row['cnpj_basico']}'");
                $updated++;
            }

            usleep(200000);
        }

        $offset += $batchSize;
        saveCheckpoint($seg, $offset);

        $pct = round(min($offset, $total) / $total * 100);
        echo "\r  $offset/$total ($pct%) | ok: $updated | err: $errors";
    }

    echo "\n  Feito: $updated atualizados, $errors erros\n";
    $updatedGeral += $updated;
    $errorsGeral += $errors;
}

// Limpa checkpoint apos conclusao
if (file_exists($checkpointFile)) unlink($checkpointFile);

$elapsed = round(microtime(true) - $start, 1);
echo "\n========== RESUMO FINAL ==========\n";
echo "Atualizados: $updatedGeral\n";
echo "Erros: $errorsGeral\n";
echo "Tempo total: ${elapsed}s (" . round($elapsed/60, 1) . "min)\n";

