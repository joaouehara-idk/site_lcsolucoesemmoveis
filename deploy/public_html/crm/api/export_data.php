<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$segments = [
    'arquitetos_ms' => 'Arquitetos',
    'designers_interiores_ms' => 'Designers de Interiores',
    'designers_ms' => 'Designers',
    'construtoras_ms' => 'Construtoras',
    'imobiliarias_ms' => 'Imobiliárias',
    'engenheiros_ms' => 'Engenheiros',
    'lojas_marcenarias_ms' => 'Lojas e Marcenarias',
    'paisagismo_decoracao_ms' => 'Paisagismo e Decoração',
];

$selected = $_GET['segments'] ?? array_keys($segments);
if (!is_array($selected)) {
    $selected = [$selected];
}
$selected = array_intersect($selected, array_keys($segments));
if (empty($selected)) {
    $selected = array_keys($segments);
}

$status = $_GET['status'] ?? 'todos';
$search = trim($_GET['q'] ?? '');
$cidade = trim($_GET['cidade'] ?? '');
$limit = min(5000, max(1, intval($_GET['limit'] ?? 200)));
$offset = max(0, intval($_GET['offset'] ?? 0));
$format = $_GET['format'] ?? 'json';

$conditions = [];
if ($status === 'ativos') {
    $conditions[] = "situacao_cadastral = '02'";
} elseif ($status === 'inativos') {
    $conditions[] = "situacao_cadastral != '02'";
} elseif ($status === 'com_telefone') {
    $conditions[] = "telefone IS NOT NULL AND telefone != ''";
} elseif ($status === 'sem_telefone') {
    $conditions[] = "(telefone IS NULL OR telefone = '')";
} elseif ($status === 'ativos_tel') {
    $conditions[] = "situacao_cadastral = '02' AND telefone IS NOT NULL AND telefone != ''";
}

if ($cidade) {
    $r = $conn->query("SELECT nome FROM municipios WHERE codigo = '" . $conn->real_escape_string($cidade) . "' AND uf = 'MS'");
    if ($r && $row = $r->fetch_assoc()) {
        $nomeCidade = $conn->real_escape_string($row['nome']);
        $conditions[] = "nome_municipio = '$nomeCidade'";
    }
}

if ($search) {
    $terms = explode(' ', $search);
    $ors = [];
    $fields = ['razao_social', 'nome_fantasia', 'bairro', 'logradouro', 'telefone', 'cnpj_basico'];
    foreach ($terms as $t) {
        $t = trim($t);
        if (strlen($t) < 2) continue;
        $sub = [];
        foreach ($fields as $f) {
            $esc = $conn->real_escape_string($t);
            $sub[] = "$f LIKE '%$esc%'";
        }
        $ors[] = '(' . implode(' OR ', $sub) . ')';
    }
    if ($ors) {
        $conditions[] = implode(' AND ', $ors);
    }
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$parts = [];
foreach ($selected as $seg) {
    $label = $segments[$seg];
    $col = "*, '$seg' as _segment, '" . $conn->real_escape_string($label) . "' as _label";
    $parts[] = "(SELECT $col FROM `$seg` $where)";
}

$unionBase = implode(' UNION ALL ', $parts);

$total = 0;
foreach ($selected as $seg) {
    $cr = $conn->query("SELECT COUNT(*) as total FROM `$seg` $where");
    if ($cr) {
        $total += (int)$cr->fetch_assoc()['total'];
    }
}

$q = "$unionBase ORDER BY razao_social LIMIT $limit OFFSET $offset";
$r = $conn->query($q);
$records = [];
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $records[] = [
            'segment' => $row['_segment'],
            'segment_label' => $row['_label'],
            'cnpj_basico' => $row['cnpj_basico'],
            'razao_social' => $row['razao_social'],
            'nome_fantasia' => $row['nome_fantasia'],
            'bairro' => $row['bairro'],
            'logradouro' => $row['logradouro'],
            'numero' => $row['numero'],
            'complemento' => $row['complemento'],
            'cep' => $row['cep'],
            'uf' => $row['uf'],
            'ddd' => $row['ddd'],
            'telefone' => $row['telefone'],
            'correio_eletronico' => $row['correio_eletronico'],
            'situacao_cadastral' => $row['situacao_cadastral'],
            'data_inicio_atividade' => $row['data_inicio_atividade'],
            'capital_social' => $row['capital_social'],
            'nome_municipio' => $row['nome_municipio'],
        ];
    }
}

echo json_encode(['total' => $total, 'limit' => $limit, 'offset' => $offset, 'records' => $records]);

