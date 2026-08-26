<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

require_once __DIR__ . '/includes/db.php';

$page = $_GET['page'] ?? ($_GET['tab'] ?? 'dashboard');
$segTable = $_GET['seg'] ?? 'arquitetos_ms';
$search = trim($_GET['q'] ?? '');
$tab = $page;

$pageNum = max(1, intval($_GET['p'] ?? 1));
$limit = 50;
$offset = ($pageNum - 1) * $limit;
$cidadeFiltro = trim($_GET['cidade'] ?? '');

$segKeys = array_keys($segments);
if (!in_array($segTable, $segKeys)) $segTable = 'arquitetos_ms';
$currentSeg = $segments[$segTable];

$cidadeWhere = '';
$cidadeNome = '';
if ($cidadeFiltro && isset($cidades[$cidadeFiltro])) {
    $cidadeNome = $cidades[$cidadeFiltro];
    $escCidade = $conn->real_escape_string($cidadeNome);
    $cidadeWhere = "nome_municipio = '$escCidade'";
}

$statsAll = [];
foreach ($segments as $table => $s) {
    $w = $cidadeWhere ? "WHERE $cidadeWhere" : 'WHERE 1=1';
    $r = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN situacao_cadastral='02' THEN 1 ELSE 0 END) as ativos, SUM(CASE WHEN telefone IS NOT NULL AND telefone != '' THEN 1 ELSE 0 END) as com_tel, COUNT(DISTINCT bairro) as bairros FROM `$table` $w");
    $statsAll[$table] = $r ? $r->fetch_assoc() : ['total' => 0, 'ativos' => 0, 'com_tel' => 0, 'bairros' => 0];
}

$stats = $statsAll[$segTable];
$total = 0;
$results = [];

$filtro = trim($_GET['f'] ?? 'todos');
$bairroFiltro = trim($_GET['bairro'] ?? '');
$capMin = trim($_GET['cap_min'] ?? '');
$capMax = trim($_GET['cap_max'] ?? '');

if ($tab === 'segment') {
    $whereClauses = [];
    if ($search) {
        $like = '%' . $conn->real_escape_string(mb_strtoupper($search)) . '%';
        $whereClauses[] = "(razao_social LIKE '$like' OR nome_fantasia LIKE '$like' OR bairro LIKE '$like' OR logradouro LIKE '$like' OR cnpj_basico LIKE '$like')";
    }
    if ($filtro === 'ativos') $whereClauses[] = "situacao_cadastral='02'";
    elseif ($filtro === 'baixadas') $whereClauses[] = "situacao_cadastral='08'";
    elseif ($filtro === 'tel' || $filtro === 'com_telefone') $whereClauses[] = "telefone IS NOT NULL AND telefone != ''";
    elseif ($filtro === 'email') $whereClauses[] = "correio_eletronico IS NOT NULL AND correio_eletronico != ''";
    elseif ($filtro === 'semtel' || $filtro === 'sem_telefone') $whereClauses[] = "(telefone IS NULL OR telefone = '')";
    if ($bairroFiltro) $whereClauses[] = "bairro = '" . $conn->real_escape_string($bairroFiltro) . "'";
    if ($capMin !== '') $whereClauses[] = "CAST(REPLACE(COALESCE(capital_social,'0'), ',', '.') AS DECIMAL(12,2)) >= " . floatval($capMin);
    if ($capMax !== '') $whereClauses[] = "CAST(REPLACE(COALESCE(capital_social,'0'), ',', '.') AS DECIMAL(12,2)) <= " . floatval($capMax);
    if ($cidadeWhere) $whereClauses[] = $cidadeWhere;

    $where = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
    $totalR = $conn->query("SELECT COUNT(*) FROM `$segTable` $where");
    $total = $totalR ? (int)$totalR->fetch_row()[0] : 0;
    $resultsR = $conn->query("SELECT *, CAST(REPLACE(COALESCE(capital_social,'0'), ',', '.') AS DECIMAL(12,2)) as capital_num FROM `$segTable` $where ORDER BY capital_num DESC LIMIT $limit OFFSET $offset");
    $results = $resultsR ? $resultsR->fetch_all(MYSQLI_ASSOC) : [];
}

$chartsData = [];
foreach ($segments as $table => $s) {
    $w = $cidadeWhere ? "WHERE $cidadeWhere" : 'WHERE 1=1';
    $bairrosR = $conn->query("SELECT bairro, COUNT(*) as total FROM `$table` $w AND bairro IS NOT NULL AND bairro != '' GROUP BY bairro ORDER BY total DESC LIMIT 10");
    $sitR = $conn->query("SELECT situacao_cadastral, COUNT(*) as total FROM `$table` $w GROUP BY situacao_cadastral");
    $chartsData[$table] = [
        'bairros' => $bairrosR ? $bairrosR->fetch_all(MYSQLI_ASSOC) : [],
        'situacao' => $sitR ? $sitR->fetch_all(MYSQLI_ASSOC) : [],
        'stats' => $statsAll[$table]
    ];
}

$totalAll = array_sum(array_column($statsAll, 'total'));
$totalAtivosAll = array_sum(array_column($statsAll, 'ativos'));
$totalTelAll = array_sum(array_column($statsAll, 'com_tel'));
$totalBairrosAll = array_sum(array_column($statsAll, 'bairros'));

$segColors = [];
$segLabels = [];
foreach ($segments as $key => $s) {
    $segColors[$key] = $s['color'];
    $segLabels[$key] = $s['label'];
}

include __DIR__ . '/includes/header.php';

$allowedPages = ['dashboard', 'funil', 'charts', 'segmentos', 'segment', 'chat', 'exportar', 'tracking', 'email', 'clientes', 'projetos', 'perfil', 'whatsapp'];
if (!in_array($page, $allowedPages)) $page = 'dashboard';

$pageFile = __DIR__ . '/pages/' . basename($page) . '.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    include __DIR__ . '/pages/dashboard.php';
}

include __DIR__ . '/includes/footer.php';
