<?php
/**
 * Script de diagnóstico do sistema de token 2FA
 * Acesse: https://lcsolucoesemmoveis.com.br/test_token_debug.php
 * DELETE após uso!
 */

require_once __DIR__ . '/includes/config.php';
require_once ROOT_PATH . '/app/Core/Database.php';
require_once ROOT_PATH . '/app/Services/TokenService.php';
require_once ROOT_PATH . '/app/Models/User.php';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico Token 2FA</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .warn { color: orange; font-weight: bold; }
        .box { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Diagnóstico Sistema de Token 2FA</h1>
    <p><strong>DELETE este arquivo após o diagnóstico!</strong></p>

<?php

function check($name, $result, $details = '') {
    $class = $result ? 'pass' : 'fail';
    echo "<div class='box'><span class='$class'>" . ($result ? 'PASS' : 'FAIL') . "</span> — $name";
    if ($details) echo "<pre>$details</pre>";
    echo "</div>";
}

// 1. Timezone
$phpTz = date_default_timezone_get();
$phpTime = date('Y-m-d H:i:s');
check("PHP Timezone", $phpTz === 'America/Campo_Grande', "Timezone: $phpTz\nHora PHP: $phpTime");

// 2. Database connection
try {
    $db = \App\Core\Database::getInstance();
    $dbRow = $db->fetch("SELECT NOW() as now, @@system_time_zone as sys_tz, @@time_zone as db_tz");
    $dbTime = $dbRow['now'] ?? 'N/A';
    $dbTz = $dbRow['db_tz'] ?? 'N/A';
    check("Conexão DB", true, "Hora MySQL: $dbTime\nTimezone MySQL: $dbTz\nTimezone Sistema: " . ($dbRow['sys_tz'] ?? 'N/A'));
} catch (Exception $e) {
    check("Conexão DB", false, $e->getMessage());
}

// 3. Tabela login_tokens existe?
try {
    $db = \App\Core\Database::getInstance();
    $exists = $db->fetch("SELECT COUNT(*) as cnt FROM login_tokens");
    check("Tabela login_tokens existe", true, "Total tokens no banco: " . ($exists['cnt'] ?? 0));
} catch (Exception $e) {
    check("Tabela login_tokens existe", false, $e->getMessage());
}

// 4. Tabela trusted_devices existe?
try {
    $db = \App\Core\Database::getInstance();
    $exists = $db->fetch("SELECT COUNT(*) as cnt FROM trusted_devices");
    check("Tabela trusted_devices existe", true, "Total devices: " . ($exists['cnt'] ?? 0));
} catch (Exception $e) {
    check("Tabela trusted_devices existe", false, $e->getMessage());
}

// 5. Usuário admin existe?
$userModel = new \App\Models\User();
$user = $userModel->findByEmail('joaomigueluehara@gmail.com');
check("Usuário admin existe", (bool)$user, $user ? "ID: {$user['id']}, usuário: {$user['usuario']}" : 'Não encontrado');

if ($user) {
    // 6. Gerar token
    $tokenService = new \App\Services\TokenService();
    $token = $tokenService->generateToken($user['id']);
    check("Gerar token", strlen($token) === 6, "Token gerado: $token");

    // 7. Verificar se token foi salvo no banco
    $saved = $db->fetch("SELECT * FROM login_tokens WHERE user_id = ? AND token = ? AND used = 0", [$user['id'], $token]);
    check("Token salvo no banco", (bool)$saved, $saved ? "ID: {$saved['id']}, expires_at: {$saved['expires_at']}" : 'Token não encontrado');

    if ($saved) {
        // 8. Comparar expires_at com NOW() do banco
        $comparison = $db->fetch("SELECT ? as token_expiry, NOW() as now, ? < NOW() as is_expired", [$saved['expires_at'], $saved['expires_at']]);
        $isExpired = (bool)$comparison['is_expired'];
        check("Token não está expirado (PHP date vs MySQL NOW)", !$isExpired,
            "expires_at (PHP date): {$saved['expires_at']}\nNOW() (MySQL): {$comparison['now']}\nExpirado? " . ($isExpired ? 'SIM' : 'NÃO'));
    }

    // 9. Validar token
    $valid = $tokenService->validateToken($user['id'], $token);
    check("Validar token (antes de marcar como usado)", $valid, "");

    // 10. Validar token novamente (deve ser falso pois foi usado)
    $valid2 = $tokenService->validateToken($user['id'], $token);
    check("Token marcado como usado (segunda validação)", !$valid2, "");
}

// 11. Testar envio de email
$tokenService = new \App\Services\TokenService();
$testToken = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
$sent = $tokenService->sendTokenEmail('joaomigueluehara@gmail.com', $testToken, 'joao');
check("Envio de email Brevo", $sent, "Token de teste: $testToken\nVerifique a caixa de entrada/spam do Gmail.");

// 12. Verificar variáveis de ambiente do Brevo
$brevoKey = $_SERVER['BREVO_API_KEY'] ?? '';
$brevoUser = $_SERVER['BREVO_SMTP_USER'] ?? '';
$brevoSender = $_SERVER['BREVO_SENDER_EMAIL'] ?? '';
check("BREVO_API_KEY configurada", strlen($brevoKey) > 10, "Comprimento: " . strlen($brevoKey));
check("BREVO_SENDER_EMAIL configurada", strlen($brevoSender) > 5, "Sender: $brevoSender");
check("BREVO_SMTP_USER configurada", strlen($brevoUser) > 5, "SMTP User: $brevoUser");

// 13. Verificar logs recentes
$logFile = ROOT_PATH . '/logs/app.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recent = array_slice($lines, -20);
    check("Logs recentes", true, implode('', $recent));
} else {
    check("Arquivo de log existe", false, "Caminho: $logFile");
}

?>
    <hr>
    <p><strong>DELETE este arquivo após o diagnóstico!</strong></p>
</body>
</html>
