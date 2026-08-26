<?php
// ============================================
// SETUP USUARIO - LC CRM
// ============================================
// Edite as credenciais abaixo e acesse:
// http://localhost/crm/setup_usuario.php
// ============================================

// === CREDENCIAIS DO USUARIO ===
$novo_nome   = 'Admin';
$novo_email  = 'lcmovel.planejadocg@gmail.com';
$novo_senha  = 'Jm@103465';
$novo_role   = 'admin'; // admin | user
// ===============================

require_once __DIR__ . '/includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

echo "<pre>";
echo "=== SETUP USUARIO - LC CRM ===\n\n";

// 1. Criar tabela usuarios
$sql = file_get_contents(__DIR__ . '/api/init_usuario.sql');
if ($conn->multi_query($sql)) {
    echo "[OK] Tabela usuarios criada/existe\n";
    while ($conn->next_result()) {;}
} else {
    echo "[ERR] Tabela: " . $conn->error . "\n";
}

// 2. Inserir usuario
$hash = password_hash($novo_senha, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT IGNORE INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $novo_nome, $novo_email, $hash, $novo_role);

if ($stmt->execute()) {
    echo "[OK] Usuario criado com sucesso!\n\n";
    echo "  Nome:  $novo_nome\n";
    echo "  Email: $novo_email\n";
    echo "  Senha: $novo_senha\n";
    echo "  Role:  $novo_role\n\n";
    echo "  Acesse: http://localhost/crm/login.php\n";
} else {
    if ($conn->errno === 1062) {
        echo "[OK] Usuario ja existe. Pulando.\n\n";
        echo "  Email: $novo_email\n";
        echo "  Role atualize manualmente se necessario.\n";
    } else {
        echo "[ERR] " . $stmt->error . "\n";
    }
}

$stmt->close();
$conn->close();

echo "\n========================================\n";
echo "Para ALTERAR a senha de um usuario existente:\n";
echo "  php -r \"echo password_hash('nova_senha', PASSWORD_BCRYPT);\"\n";
echo "  Copie o hash e execute via phpmyadmin:\n";
echo "  UPDATE usuarios SET senha_hash = 'hash_aqui' WHERE email = 'admin@lcrm.com';\n";
echo "========================================\n";
echo "</pre>";

