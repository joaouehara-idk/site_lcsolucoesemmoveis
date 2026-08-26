<?php
/**
 * Script para executar migrações SQL do blog
 * ATENÇÃO: DELETE este arquivo após usar!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Credenciais de produção
$db_host = 'localhost';
$db_name = 'luizc159_lcsolucoes_site';
$db_user = 'luizc159_joao';
$db_pass = 'Jm@10653407388336141$';

echo "<h1>Executar Migrações SQL do Blog</h1>";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✅ Conectado ao banco de dados</p>";
} catch (PDOException $e) {
    die("<p style='color:red'>❌ Erro de conexão: " . $e->getMessage() . "</p>");
}

// Listar migrações disponíveis
$migrationDir = __DIR__ . '/migrations/';
$migrations = [
    'migration_blog_2026_08_20.sql' => 'Fase 0-1: Correções + Posts 8-12',
    'migration_blog_fase2_2026_08_20.sql' => 'Fase 2: Posts 13-17',
    'migration_blog_fase3_2026_08_20.sql' => 'Fase 3: Posts 18-22',
    'migration_blog_fase4_2026_08_20.sql' => 'Fase 4: Posts 23-32',
];

// Verificar quantos posts já existem
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$existing = $stmt->fetch()['total'];
echo "<p>Posts existentes no banco: <strong>$existing</strong></p>";

// Verificar categorias
$stmt = $pdo->query("SELECT id, nome, slug FROM categorias ORDER BY id");
$cats = $stmt->fetchAll();
echo "<p>Categorias existentes:</p><ul>";
foreach ($cats as $c) {
    echo "<li>ID {$c['id']}: {$c['nome']} ({$c['slug']})</li>";
}
echo "</ul>";

// Executar cada migração
foreach ($migrations as $file => $description) {
    echo "<hr><h2>$description</h2>";
    $filepath = $migrationDir . $file;
    
    if (!file_exists($filepath)) {
        echo "<p style='color:orange'>⚠️ Arquivo não encontrado: $file</p>";
        $filepath = __DIR__ . '/database/' . $file;
        if (!file_exists($filepath)) {
            $filepath = __DIR__ . '/' . $file;
            if (!file_exists($filepath)) {
                echo "<p style='color:red'>❌ Arquivo não encontrado em nenhum caminho</p>";
                continue;
            }
        }
    }
    
    $sql = file_get_contents($filepath);
    
    // Dividir por declarações SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = 0;
    $errors = 0;
    $skipped = 0;
    
    foreach ($statements as $stmt_sql) {
        $stmt_sql = trim($stmt_sql);
        if (empty($stmt_sql) || strpos($stmt_sql, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($stmt_sql);
            $success++;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate') !== false || strpos($msg, 'already exists') !== false) {
                $skipped++;
            } else {
                echo "<p style='color:red'>❌ Erro: " . htmlspecialchars($msg) . "</p>";
                echo "<pre style='background:#f5f5f5;padding:10px;overflow-x:auto;font-size:12px;'>" . htmlspecialchars(substr($stmt_sql, 0, 300)) . "</pre>";
                $errors++;
            }
        }
    }
    
    echo "<p>✅ Sucesso: $success | ⏭️ Ignorados (duplicados): $skipped | ❌ Erros: $errors</p>";
}

// Verificar resultado final
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$final = $stmt->fetch()['total'];
echo "<hr><h2>Resultado Final</h2>";
echo "<p>Posts no banco: <strong>$final</strong> (era $existing)</p>";

// Listar todos os posts
$stmt = $pdo->query("SELECT id, titulo, slug, status, categoria FROM posts ORDER BY id");
$posts = $stmt->fetchAll();
echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;width:100%'>";
echo "<tr><th>ID</th><th>Título</th><th>Slug</th><th>Status</th><th>Categoria</th></tr>";
foreach ($posts as $p) {
    $color = $p['status'] === 'publicado' ? 'green' : 'gray';
    echo "<tr><td>{$p['id']}</td><td>" . htmlspecialchars($p['titulo']) . "</td><td>{$p['slug']}</td><td style='color:$color'>{$p['status']}</td><td>{$p['categoria']}</td></tr>";
}
echo "</table>";

echo "<hr><p style='color:red;font-weight:bold'>⚠️ DELETE este arquivo após usar! Arquivo: execute_migrations.php</p>";
?>
