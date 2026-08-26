<?php
/**
 * Centralized Database Configuration - LC CRM
 * 
 * Configure via environment variables or edit defaults below.
 * 
 * On HostGator production:
 *   define('CRM_DB_HOST', 'localhost');
 *   define('CRM_DB_NAME', 'luizca93_crm_cnpj');
 *   define('CRM_DB_USER', 'luizca93_joao');
 *   define('CRM_DB_PASS', 'sua_senha_aqui');
 */

if (!defined('CRM_DB_HOST')) {
    define('CRM_DB_HOST', getenv('CRM_DB_HOST') ?: 'localhost');
    define('CRM_DB_NAME', getenv('CRM_DB_NAME') ?: 'luizc159_lcsolucoes_site');
    define('CRM_DB_USER', getenv('CRM_DB_USER') ?: 'luizc159_joao');
    define('CRM_DB_PASS', getenv('CRM_DB_PASS') ?: 'Jm@10653407388336141$');
    define('CRM_DB_PORT', getenv('CRM_DB_PORT') ?: '3306');
}

function getDbConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(CRM_DB_HOST, CRM_DB_USER, CRM_DB_PASS, CRM_DB_NAME, (int)CRM_DB_PORT);
        if ($conn->connect_error) {
            if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                die(json_encode(['success' => false, 'error' => 'Database connection failed']));
            }
            die('Database connection failed. Please check db_config.php');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
