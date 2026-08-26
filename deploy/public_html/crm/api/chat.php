<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$config = [];
$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    $config = include $configPath;
}

// ─── Gerar mensagem WhatsApp personalizada ───
if (isset($_GET['action']) && $_GET['action'] === 'gerar_mensagem') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nome = $input['nome'] ?? '';
    $segmento = $input['segmento'] ?? '';
    $cidade = $input['cidade'] ?? '';
    $bairro = $input['bairro'] ?? '';
    $tipo = $input['tipo'] ?? 'parceria';

    if (!$nome) {
        echo json_encode(['response' => 'Selecione um contato primeiro.']);
        exit;
    }

    $tiposMsg = [
        'parceria' => 'propondo uma parceria comercial. A LC Soluções em Móveis é uma marcenaria especializada em móveis planejados em Mato Grosso do Sul e quer oferecer seus produtos e serviços como opção para os clientes do parceiro.',
        'orcamento' => 'oferecendo um orçamento especial para móveis planejados. A LC Soluções em Móveis quer apresentar soluções para o próximo projeto do parceiro.',
        'apresentacao' => 'se apresentando como nova opção em móveis planejados. A LC Soluções em Móveis quer mostrar seu portfólio e diferenciais.',
        'followup' => 'fazendo um acompanhamento após contato anterior. Reforçar o interesse da LC Soluções em Móveis em colaborar com o parceiro.',
    ];
    $descricaoTipo = $tiposMsg[$tipo] ?? $tiposMsg['parceria'];

    $infoExtra = '';
    if ($segmento) $infoExtra .= " O contato é do segmento $segmento.";
    if ($cidade) $infoExtra .= " Fica em $cidade.";
    if ($bairro) $infoExtra .= " Bairro: $bairro.";

    $systemPrompt = "Você é um assistente de vendas da LC Soluções em Móveis, uma marcenaria especializada em móveis planejados em Mato Grosso do Sul. ";
    $systemPrompt .= "Sua função é GERAR APENAS o texto da mensagem de WhatsApp, sem explicações, sem markdown, sem cumprimentos do assistente. ";
    $systemPrompt .= "A mensagem deve ser curta (máximo 3 parágrafos), profissional, personalizada e amigável. Inclua o nome do contato. ";
    $systemPrompt .= "Use emojis com moderação (máximo 2). Termine com um call-to-action sutil (como 'Vamos conversar?' ou 'Posso enviar mais informações?'). ";
    $systemPrompt .= "Gere APENAS o texto da mensagem, nada mais.";

    $message = "Crie uma mensagem de WhatsApp personalizada para $nome." . $infoExtra . " Objetivo: " . $descricaoTipo;

    $openaiKey = $config['openai_api_key'] ?? '';
    if ($openaiKey) {
        $resp = askOpenAI($message, [], $openaiKey, $systemPrompt);
        if ($resp) { echo json_encode(['response' => trim($resp)]); exit; }
    }

    $groqKey = $config['groq_api_key'] ?? '';
    if ($groqKey) {
        $resp = askGroq($message, [], $groqKey, $systemPrompt);
        if ($resp) { echo json_encode(['response' => trim($resp)]); exit; }
    }

    $fallback = "Olá $nome! Aqui é da LC Soluções em Móveis, somos especializados em móveis planejados em MS.";
    if ($segmento) $fallback .= " Vimos que você atua em $segmento.";
    $fallback .= " Gostaríamos de apresentar nossas soluções para parceria. Vamos conversar?";
    echo json_encode(['response' => $fallback]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['response' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$history = $input['history'] ?? [];

if (!$message) {
    echo json_encode(['response' => 'Digite uma mensagem.']);
    exit;
}

$msg = mb_strtolower($message);

// ========================================
// PROSPECÇÃO - Filtragem inteligente (deve vir antes do OpenAI)
// ========================================

$segments = [
    'arquitetos_ms' => ['label' => 'Arquitetos', 'aliases' => ['arquiteto','arquitetos','arquitetura']],
    'designers_interiores_ms' => ['label' => 'Designers de Interiores', 'aliases' => ['designer de interiores','designers de interiores','design interiores','decorador','decoradores']],
    'designers_ms' => ['label' => 'Designers', 'aliases' => ['designer','designers','design gráfico','design produto']],
    'construtoras_ms' => ['label' => 'Construtoras e Incorporadoras', 'aliases' => ['construtora','construtoras','incorporadora','incorporadoras','construção civil']],
    'imobiliarias_ms' => ['label' => 'Imobiliárias', 'aliases' => ['imobiliária','imobiliarias','imobiliárias','corretor','corretores','corretagem']],
    'engenheiros_ms' => ['label' => 'Engenheiros', 'aliases' => ['engenheiro','engenheiros','engenharia','eng civil']],
    'lojas_marcenarias_ms' => ['label' => 'Lojas e Marcenarias', 'aliases' => ['marcenaria','marcenarias','loja de móveis','lojas de móveis','montagem','armário embutido']],
    'paisagismo_decoracao_ms' => ['label' => 'Paisagismo e Decoração', 'aliases' => ['paisagismo','paisagista','paisagistas','decoração','decoracao','jardinagem']],
];

$segmentoAliases = [];
foreach ($segments as $table => $seg) {
    foreach ($seg['aliases'] as $a) {
        $segmentoAliases[$a] = $table;
    }
}

$cidades = [];
$r = $conn->query("SELECT codigo, nome FROM municipios WHERE uf = 'MS' ORDER BY nome");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $cidades[$row['codigo']] = $row['nome'];
    }
}

$stats = [];
foreach ($segments as $table => $seg) {
    $r = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN situacao_cadastral='02' THEN 1 ELSE 0 END) as ativos, SUM(CASE WHEN telefone IS NOT NULL AND telefone != '' THEN 1 ELSE 0 END) as com_tel FROM `$table`");
    $stats[$table] = $r ? $r->fetch_assoc() : ['total' => 0, 'ativos' => 0, 'com_tel' => 0];
}

$totalAll = array_sum(array_column($stats, 'total'));
$totalAtivosAll = array_sum(array_column($stats, 'ativos'));
$totalTelAll = array_sum(array_column($stats, 'com_tel'));

function detectSegment($msg, $segments) {
    foreach ($segments as $table => $seg) {
        foreach ($seg['aliases'] as $a) {
            if (mb_strpos($msg, $a) !== false) return [$table, $seg['label']];
        }
    }
    return [null, null];
}

function webSearchDDG($query) {
    $url = 'https://api.duckduckgo.com/?q=' . urlencode($query) . '&format=json&no_html=1&skip_disambig=1';
    $ctx = stream_context_create(['http' => ['timeout' => 8, 'user_agent' => 'Mozilla/5.0']]);
    $resp = file_get_contents($url, false, $ctx);
    if ($resp === false) return null;
    $data = json_decode($resp, true);
    if (!$data) return null;

    $parts = [];
    if (!empty($data['AbstractText'])) $parts[] = $data['AbstractText'];
    if (!empty($data['Definition'])) $parts[] = $data['Definition'];
    if (!empty($data['Infobox']['content']) && is_array($data['Infobox']['content'])) {
        foreach ($data['Infobox']['content'] as $item) {
            if (!empty($item['label']) && !empty($item['value'])) $parts[] = $item['label'] . ': ' . $item['value'];
        }
    }
    if (!empty($data['RelatedTopics'])) {
        foreach (array_slice($data['RelatedTopics'], 0, 3) as $topic) {
            if (is_array($topic) && !empty($topic['Text'])) $parts[] = $topic['Text'];
            elseif (is_array($topic) && !empty($topic['Result'])) $parts[] = strip_tags($topic['Result']);
        }
    }
    if (!empty($data['Answer'])) array_unshift($parts, $data['Answer']);

    return empty($parts) ? null : implode("\n\n", $parts);
}

function askOpenAI($message, $history, $apiKey, $systemPrompt = null) {
    $defaultPrompt = 'Você é um assistente de CRM especializado em dados de parceiros comerciais em Mato Grosso do Sul. Responda em português brasileiro de forma clara e direta.';
    $messages = [['role' => 'system', 'content' => $systemPrompt ?: $defaultPrompt]];
    foreach ($history as $h) $messages[] = ['role' => $h['role'], 'content' => $h['text']];
    $messages[] = ['role' => 'user', 'content' => $message];

    $body = json_encode([
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'max_tokens' => 1000,
        'temperature' => 0.7
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
            'content' => $body,
            'timeout' => 30
        ]
    ]);

    $resp = file_get_contents('https://api.openai.com/v1/chat/completions', false, $ctx);
    if ($resp === false) return null;
    $data = json_decode($resp, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

function askGroq($message, $history, $apiKey, $systemPrompt = null) {
    if (!$apiKey) return null;
    $defaultPrompt = 'Você é um assistente de CRM especializado em dados de parceiros comerciais em Mato Grosso do Sul. Responda em português brasileiro de forma clara e direta.';
    $messages = [['role' => 'system', 'content' => $systemPrompt ?: $defaultPrompt]];
    foreach ($history as $h) $messages[] = ['role' => $h['role'], 'content' => $h['text']];
    $messages[] = ['role' => 'user', 'content' => $message];

    $body = json_encode([
        'model' => 'llama-3.3-70b-versatile',
        'messages' => $messages,
        'max_tokens' => 1000,
        'temperature' => 0.7
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
            'content' => $body,
            'timeout' => 30
        ]
    ]);

    $resp = file_get_contents('https://api.groq.com/openai/v1/chat/completions', false, $ctx);
    if ($resp === false) return null;
    $data = json_decode($resp, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

// ========================================
// PROSPECÇÃO - Filtragem inteligente (consulta real no banco)
// ========================================

// Detectar cidade na mensagem
$cidadeDetectada = null;
foreach ($cidades as $cod => $nome) {
    $nomeLower = mb_strtolower($nome);
    if (mb_strpos($msg, $nomeLower) !== false) {
        $cidadeDetectada = $nome;
        break;
    }
}

// Filtrar por múltiplos critérios
if (preg_match('/(mostre|lista|liste|filtre|filtra|quero|busque|encontre|prospects?|prospecção)/i', $msg)) {
    $tableTargets = [];
    $conditions = [];
    $params = [];
    $types = '';

    // Detectar segmentos
    foreach ($segments as $table => $seg) {
        foreach ($seg['aliases'] as $a) {
            if (mb_strpos($msg, $a) !== false) {
                $tableTargets[] = $table;
                break;
            }
        }
    }
    if (empty($tableTargets)) $tableTargets = array_keys($segments);

    // Filtro cidade
    if ($cidadeDetectada) {
        $conditions[] = "nome_municipio = ?";
        $params[] = $cidadeDetectada;
        $types .= 's';
    }

    // Filtro ativos/inativos
    if (preg_match('/(ativos?|situação cadastral)/i', $msg)) {
        $conditions[] = "situacao_cadastral = '02'";
    } elseif (preg_match('/(inativos?|baixad[ao]s?)/i', $msg)) {
        $conditions[] = "situacao_cadastral = '08'";
    }

    // Filtro telefone
    if (preg_match('/com telefone|com tel|tem telefone|telefone disponível/i', $msg)) {
        $conditions[] = "telefone IS NOT NULL AND telefone != ''";
    } elseif (preg_match('/sem telefone|sem tel|não tem telefone|sem contato/i', $msg)) {
        $conditions[] = "(telefone IS NULL OR telefone = '')";
    }

    // Filtro email
    if (preg_match('/com email|tem email|email disponível/i', $msg)) {
        $conditions[] = "correio_eletronico IS NOT NULL AND correio_eletronico != ''";
    } elseif (preg_match('/sem email|sem e-?mail/i', $msg)) {
        $conditions[] = "(correio_eletronico IS NULL OR correio_eletronico = '')";
    }

    // Filtro capital social (mínimo)
    if (preg_match('/capital.*(maior|acima|mínimo|>).*?([\d.]+)/i', $msg, $m)) {
        $val = floatval(str_replace('.', '', $m[2]));
        $conditions[] = "capital_social >= ?";
        $params[] = $val;
        $types .= 'd';
    }

    // Filtro bairro
    if (preg_match('/bairro\s+(.+?)(?:\s+(?:com|em|no|da|do|que|e|ou|para)$)?/i', $msg, $m)) {
        $bairroTerm = trim($m[1]);
        $conditions[] = "bairro LIKE ?";
        $params[] = '%' . mb_strtoupper($bairroTerm) . '%';
        $types .= 's';
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    // Contar total e buscar amostra
    $total = 0;
    $limit = min(20, intval(preg_match('/limite\s*[:]?\s*(\d+)/i', $msg, $m) ? $m[1] : 10));

    $allResults = [];
    foreach ($tableTargets as $table) {
        $segLabel = $segments[$table]['label'];
        $sql = "SELECT razao_social, nome_fantasia, bairro, nome_municipio, ddd, telefone, correio_eletronico, situacao_cadastral, capital_social FROM `$table` $where ORDER BY capital_social DESC LIMIT $limit";
        if (!empty($params)) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM `$table` $where");
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $stmt->bind_result($cnt);
                $stmt->fetch();
                $total += $cnt;
                $stmt->close();
            }

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                $rows = [];
            }
        } else {
            $totalR = $conn->query("SELECT COUNT(*) FROM `$table`");
            $total += $totalR ? (int)$totalR->fetch_row()[0] : 0;
            $rows = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        }

        foreach ($rows as $r) {
            $r['_segmento'] = $segLabel;
            $allResults[] = $r;
        }
    }

    if ($total > 0) {
        $filtrosDesc = [];
        $segmentoDesc = count($tableTargets) === 1 ? $segments[$tableTargets[0]]['label'] : count($tableTargets) . ' segmentos';
        $filtrosDesc[] = $segmentoDesc;
        if ($cidadeDetectada) $filtrosDesc[] = "em $cidadeDetectada";
        if (preg_match('/com telefone|tem telefone/i', $msg)) $filtrosDesc[] = "com telefone";
        if (preg_match('/sem telefone/i', $msg)) $filtrosDesc[] = "sem telefone";
        if (preg_match('/ativos?/i', $msg)) $filtrosDesc[] = "ativos";
        if (preg_match('/inativos?/i', $msg)) $filtrosDesc[] = "inativos";

        $lines = ["**Resultado da prospecção: " . implode(', ', $filtrosDesc) . "**\n"];
        $lines[] = "**{$total} registros encontrados** (exibindo {$limit} por segmento)\n";

        $count = 0;
        foreach ($allResults as $r) {
            if ($count >= 20) break;
            $tel = $r['ddd'] && $r['telefone'] ? "({$r['ddd']}) {$r['telefone']}" : 'sem telefone';
            $email = $r['correio_eletronico'] ?: 'sem email';
            $capVal = floatval(str_replace(',', '.', str_replace('.', '', strval($r['capital_social'] ?? '0'))));
            $cap = $capVal > 0 ? 'R$ ' . number_format($capVal, 2, ',', '.') : '-';
            $lines[] = "• **{$r['razao_social']}** — {$r['_segmento']}";
            $lines[] = "  {$r['bairro']}, {$r['nome_municipio']} | 📞 {$tel} | ✉️ {$email} | 💰 {$cap}";
            $count++;
        }

        if ($count < $total) {
            $lines[] = "\n_E mais " . ($total - $count) . " registros. Seja mais específico para refinar._";
        }

        // Sugestões de refino
        $sugestoes = [];
        if (!$cidadeDetectada && $total > 5) $sugestoes[] = "filtrar por cidade";
        if (!preg_match('/com telefone/i', $msg)) $sugestoes[] = "filtrar apenas com telefone";
        if (!preg_match('/ativos?/i', $msg)) $sugestoes[] = "filtrar apenas ativos";
        if (count($tableTargets) > 1) $sugestoes[] = "especificar um segmento";
        if ($sugestoes) {
            $lines[] = "\n💡 **Sugestões:** " . implode(', ', $sugestoes) . " para refinar.";
        }

        echo json_encode(['response' => implode("\n", $lines)]);
        exit;
    }
}

list($detectedTable, $detectedLabel) = detectSegment($msg, $segments);

// Ativos vs inativos
if ($detectedTable && (preg_match('/(ativos?|inativos?|baixados?|situação|status)/i', $msg) ||
    preg_match('/quantos.*(ativos?|baixados?)/i', $msg))) {
    $s = $stats[$detectedTable];
    echo json_encode(['response' => "Dos **{$s['total']} {$detectedLabel}** em MS, **{$s['ativos']} estão ativos** e **" . ($s['total'] - $s['ativos']) . "** não estão ativos (baixadas, inaptas, suspensas, nulas)."]);
    exit;
}

// Com telefone
if ($detectedTable && preg_match('/(telefone|contato|whatsapp|tel).*(dispon[ií]vel|tem|com)/i', $msg)) {
    $s = $stats[$detectedTable];
    $pct = $s['total'] > 0 ? round($s['com_tel'] / $s['total'] * 100) : 0;
    echo json_encode(['response' => "**{$s['com_tel']}** de **{$s['total']}** {$detectedLabel} ({$pct}%) possuem telefone cadastrado."]);
    exit;
}

// Bairros com mais
if ($detectedTable && preg_match('/(bairros?|regiões?).*(mais|top|ranking)/i', $msg)) {
    $bairros = $conn->query("SELECT bairro, COUNT(*) as total FROM `$detectedTable` WHERE bairro IS NOT NULL AND bairro != '' GROUP BY bairro ORDER BY total DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
    if (count($bairros)) {
        $lines = ["Top 10 bairros com mais {$detectedLabel}:\n"];
        foreach ($bairros as $b) $lines[] = "- **{$b['bairro']}**: {$b['total']}";
        echo json_encode(['response' => implode("\n", $lines)]);
    } else {
        echo json_encode(['response' => "Não há dados de bairro para {$detectedLabel}."]);
    }
    exit;
}

// Busca por bairro
$bairroMatch = null;
if ($detectedTable) {
    if (preg_match('/\b(?:no|na|em|do|da)\s+bairro\s+(.+)/i', $msg, $m)) {
        $bairroMatch = trim($m[1]);
    } elseif (preg_match('/\b(?:no|na|em|do|da)\s+(.+)/i', $msg, $m)) {
        $bairroMatch = trim($m[1]);
    }
}
if ($bairroMatch) {
    $bairro = str_replace(['?', '!', '.', ','], '', $bairroMatch);
    $bairroUpper = mb_strtoupper($bairro);
    $stmt = $conn->prepare("SELECT razao_social, nome_fantasia, bairro, ddd, telefone, logradouro, numero FROM `$detectedTable` WHERE bairro LIKE ? LIMIT 15");
    $like = "%$bairroUpper%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($rows)) {
        $lines = ["Encontrei **" . count($rows) . "** {$detectedLabel} no bairro **{$rows[0]['bairro']}**:\n"];
        foreach ($rows as $r) {
            $tel = $r['ddd'] && $r['telefone'] ? " | Tel: ({$r['ddd']}) {$r['telefone']}" : "";
            $end = trim("{$r['logradouro']}, {$r['numero']}", ', ');
            $lines[] = "- **{$r['razao_social']}**{$tel}" . ($end ? " | {$end}" : "");
        }
        if (count($rows) === 15) $lines[] = "\n_Mostrando 15 resultados. Seja mais específico para ver mais._";
        echo json_encode(['response' => implode("\n", $lines)]);
    } else {
        echo json_encode(['response' => "Não encontrei {$detectedLabel} no bairro \"{$bairro}\"."]);
    }
    exit;
}

// Ajuda (apenas comandos explícitos)
if (preg_match('/^(ajuda|help|comandos)\s*$/i', trim($msg))) {
    $lines = ["Sou o assistente do **CRM de Parceiros LC** (LC Soluções em Móveis)\n\nBase com **{$totalAll} parceiros potenciais** em Mato Grosso do Sul:\n"];
    foreach ($segments as $table => $seg) {
        $s = $stats[$table];
        $lines[] = "- **{$seg['label']}**: {$s['total']} registros";
    }
    $lines[] = "\nPergunte: quantos existem?, mostre no bairro..., ativos vs inativos, com telefone, bairros com mais...";
    $lines[] = "Ou faça perguntas gerais sobre qualquer assunto.";
    echo json_encode(['response' => implode("\n", $lines)]);
    exit;
}

// ========================================
// IA Generativa - conversa geral
// Tenta OpenAI → Groq → DuckDuckGo → fallback
// ========================================

$segLines = [];
foreach ($segments as $table => $seg) {
    $s = $stats[$table];
    $segLines[] = "- {$seg['label']}: {$s['total']} registros, {$s['ativos']} ativos, {$s['com_tel']} com telefone";
}
$statsText = "Base de dados do CRM (MS):\n" . implode("\n", $segLines);
$statsText .= "\n\nTotal geral: {$totalAll} parceiros potenciais";
$statsText .= "\nVocê pode consultar dados detalhados diretamente nas abas do CRM (Segmentos, Funil).";

$systemPrompt = "Você é o assistente do CRM de Parceiros da LC Soluções em Móveis (móveis planejados, MS). ";
$systemPrompt .= "Seja **conciso e direto**. Use parágrafos curtos (máx 2 linhas cada) e listas com tópicos quando ajudar. ";
$systemPrompt .= "Evite repetições, rodeios e texto genérico. Vá direto ao ponto útil. ";
$systemPrompt .= "Contexto do CRM:\n" . $statsText . "\n";
$systemPrompt .= "Responda em português brasileiro claro e objetivo.";

// 1. OpenAI (se tiver cota)
$openaiKey = $config['openai_api_key'] ?? '';
if ($openaiKey) {
    $aiResponse = askOpenAI($message, $history, $openaiKey, $systemPrompt);
    if ($aiResponse) {
        echo json_encode(['response' => $aiResponse]);
        exit;
    }
}

// 2. Groq (gratuito, Llama 3)
$groqKey = $config['groq_api_key'] ?? '';
if ($groqKey) {
    $aiResponse = askGroq($message, $history, $groqKey, $systemPrompt);
    if ($aiResponse) {
        echo json_encode(['response' => $aiResponse]);
        exit;
    }
}

// 3. Pesquisa na web (DuckDuckGo)
$webResult = webSearchDDG($message);
if ($webResult) {
    echo json_encode(['response' => "**Pesquisa na web:**\n\n" . $webResult]);
    exit;
}

echo json_encode(['response' => "Não encontrei informações sobre isso. Pergunte no CRM ou tente novamente."]);

