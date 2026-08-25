<?php
$pdo = new PDO('mysql:host=localhost;dbname=meusite_db;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query('SELECT p.id, p.titulo, p.categoria, p.imagem_capa, COUNT(pi.id) as img_count FROM projetos p LEFT JOIN projeto_imagens pi ON pi.projeto_id = p.id GROUP BY p.id ORDER BY p.id');

echo "=== ALL PROJECTS ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID={$row['id']} | Cat=" . ($row['categoria'] ?? 'NULL') . " | imgs={$row['img_count']}\n";
    echo "  Title: {$row['titulo']}\n";
    echo "  Cover: {$row['imagem_capa']}\n\n";
}

echo "\n=== IMAGES PER PROJECT ===\n";
$stmt2 = $pdo->query('SELECT projeto_id, caminho, ordem FROM projeto_imagens ORDER BY projeto_id, ordem');
$counts = [];
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $pid = $row['projeto_id'];
    if (!isset($counts[$pid])) $counts[$pid] = 0;
    $counts[$pid]++;
    if ($counts[$pid] <= 3) {
        echo "  Proj $pid: " . basename($row['caminho']) . "\n";
    }
}
echo "\nTotal images: " . implode(', ', array_map(fn($k,$v) => "$k=$v", array_keys($counts), $counts)) . "\n";
