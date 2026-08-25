<?php
$pdo = new PDO('mysql:host=localhost;dbname=meusite_db;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query('SELECT id, titulo, categoria, descricao FROM projetos ORDER BY id');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "=== ID={$row['id']} | {$row['categoria']} ===\n";
    echo "Title: {$row['titulo']}\n";
    echo "Desc (first 300): " . substr(strip_tags($row['descricao']), 0, 300) . "\n\n";
}
