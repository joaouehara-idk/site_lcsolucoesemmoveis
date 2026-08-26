<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();

// Check municipios structure
$r = $conn->query("DESCRIBE municipios");
echo "=== municipios structure ===\n";
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} ({$row['Type']})\n";
}

// Check some records with UF
echo "\n=== municipios MS (codigo starts with what?) ===\n";
$r = $conn->query("SELECT codigo, nome, uf FROM municipios LIMIT 100");
$ms_codes = [];
$ufs = [];
while ($row = $r->fetch_assoc()) {
    $uf = trim($row['uf']);
    if (!isset($ufs[$uf])) $ufs[$uf] = 0;
    $ufs[$uf]++;
    if ($uf === 'MS') {
        $ms_codes[] = $row;
    }
}
echo "UF distribution: " . json_encode($ufs) . "\n";
echo "MS cities found: " . count($ms_codes) . "\n";
foreach ($ms_codes as $c) {
    echo "  {$c['codigo']} => {$c['nome']}\n";
}

echo "\n=== All codes starting with 9? ===\n";
$r = $conn->query("SELECT codigo, nome, uf FROM municipios WHERE codigo LIKE '9%' LIMIT 30");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['codigo']} => {$row['nome']} / {$row['uf']}\n";
}

// Check what distinct municipio values exist in the segment tables
echo "\n=== Distinct municipio codes in arquitetos_cg ===\n";
$r = $conn->query("SELECT DISTINCT municipio FROM arquitetos_cg ORDER BY municipio");
while ($row = $r->fetch_assoc()) {
    $cod = $row['municipio'];
    $rn = $conn->query("SELECT nome, uf FROM municipios WHERE codigo = '$cod'");
    $nome = $rn->fetch_assoc();
    echo "  $cod => " . ($nome ? $nome['nome'] . '/' . $nome['uf'] : 'NOT FOUND') . "\n";
}

