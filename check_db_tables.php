<?php
require_once __DIR__ . '/includes/config.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Criar tabela projeto_imagens se não existir
    $sql = "CREATE TABLE IF NOT EXISTS projeto_imagens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        projeto_id INT NOT NULL,
        caminho VARCHAR(255) NOT NULL,
        ordem INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    
    echo "Sucesso: Tabela projeto_imagens verificada/criada.";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
