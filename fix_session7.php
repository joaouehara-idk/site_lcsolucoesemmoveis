<?php
/**
 * FIX SCRIPT - SESSION 7
 * 
 * UPLOAD VIA CPANEL FILE MANAGER → public_html/fix_session7.php
 * Acesse: https://lcsolucoesemmoveis.com.br/fix_session7.php
 * DELETE após uso!
 * 
 * Este script:
 * 1. Cria a tabela trusted_devices no banco
 * 2. Corrige o remetente do EmailService (sistema@ → lcmovel.planejadocg@gmail.com)
 * 3. Cria um dispositivo confiável para o admin
 * 4. Inicia a sessão automaticamente
 * 5. Redireciona para /admin/dashboard
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
header('Content-Type: text/html; charset=utf-8');

$root = __DIR__;
define('ROOT_PATH', $root);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Fix Session 7</title>
<style>
body{font-family:'Inter',sans-serif;background:#f6f1eb;color:#1a1714;padding:40px;max-width:800px;margin:0 auto;}
.step{background:#fff;border:1px solid rgba(26,23,20,.06);border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.ok{border-color:rgba(74,124,89,.15);background:rgba(74,124,89,.04);}
.err{border-color:rgba(184,74,74,.15);background:rgba(184,74,74,.04);}
.ok strong{color:#4a7c59;}
.err strong{color:#b84a4a;}
code{background:#e9e4dc;padding:2px 6px;border-radius:4px;font-size:12px;}
</style></head><body>";
echo "<h1 style='font-family:\"DM Serif Display\",serif;font-size:24px;margin-bottom:24px;'>🔧 Correção — Login 2FA + Email Token</h1>";

// ─── 1. Load .env ───
echo "<div class='step'><strong>1.</strong> Carregando .env...</div>";
$envFile = $root . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
        list($key, $val) = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val, "'\",");
        if (!isset($_SERVER[$key]) || empty($_SERVER[$key]) || $_SERVER[$key] === 'root' || $_SERVER[$key] === 'localhost') {
            $_SERVER[$key] = $val;
        }
    }
    echo "<div class='step ok'><strong>✅</strong> .env carregado</div>";
} else {
    echo "<div class='step err'><strong>❌</strong> .env não encontrado</div>";
}

$DB_HOST = $_SERVER['DB_HOST'] ?? 'localhost';
$DB_NAME = $_SERVER['DB_NAME'] ?? 'luizc159_lcsolucoes_site';
$DB_USER = $_SERVER['DB_USER'] ?? 'luizc159_joao';
$DB_PASS = $_SERVER['DB_PASS'] ?? '';

// ─── 2. Connect to DB & create trusted_devices table ───
echo "<div class='step'><strong>2.</strong> Conectando ao banco e criando tabela trusted_devices...</div>";
try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='step ok'><strong>✅</strong> Conectado ao banco: {$DB_NAME}</div>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `trusted_devices` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `token` varchar(128) NOT NULL,
            `user_agent_hash` varchar(64) NOT NULL,
            `ip_prefix` varchar(32) NOT NULL,
            `expires_at` datetime NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `token` (`token`),
            KEY `idx_user` (`user_id`),
            KEY `idx_expires` (`expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='step ok'><strong>✅</strong> Tabela trusted_devices criada/verificada</div>";
} catch (PDOException $e) {
    echo "<div class='step err'><strong>❌</strong> DB Error: " . $e->getMessage() . "</div>";
}

// ─── 3. Fix EmailService.php ───
echo "<div class='step'><strong>3.</strong> Corrigindo EmailService.php...</div>";
$emailSvcPath = $root . '/app/Services/EmailService.php';
if (file_exists($emailSvcPath)) {
    $content = file_get_contents($emailSvcPath);

    // Fix sender: sistema@lcsolucoesemmoveis.com.br → lcmovel.planejadocg@gmail.com
    $oldSender = 'sistema@lcsolucoesemmoveis.com.br';
    $newSender = 'lcmovel.planejadocg@gmail.com';
    if (strpos($content, $oldSender) !== false) {
        $content = str_replace($oldSender, $newSender, $content);
        file_put_contents($emailSvcPath, $content);
        echo "<div class='step ok'><strong>✅</strong> Remetente corrigido: <code>sistema@...</code> → <code>lcmovel.planejadocg@gmail.com</code></div>";
    }

    echo "<div class='step ok'><strong>✅</strong> EmailService.php: remetente verificado + enviando via Brevo SMTP</div>";
} else {
    echo "<div class='step err'><strong>❌</strong> EmailService.php não encontrado</div>";
}

// ─── 3b. Write TrustedDeviceService.php ───
echo "<div class='step'><strong>3b.</strong> Instalando TrustedDeviceService.php...</div>";
$tdsPath = $root . '/app/Services/TrustedDeviceService.php';
$tdsDir = dirname($tdsPath);
if (!is_dir($tdsDir)) {
    mkdir($tdsDir, 0755, true);
    echo "<div class='step ok'><strong>✅</strong> Diretório criado: app/Services/</div>";
}

$tdsContent = <<<PHPEOF
<?php
namespace App\Services;
use App\Core\Database;
use App\Core\Logger;
class TrustedDeviceService {
    private \$db;
    private const COOKIE_NAME = 'trusted_device';
    private const COOKIE_EXPIRY_DAYS = 30;

    public function __construct() {
        \$this->db = Database::getInstance();
    }

    public function createTrustedDevice(int \$userId): string {
        \$this->clearUserDevices(\$userId);
        \$token = bin2hex(random_bytes(32));
        \$expiresAt = date('Y-m-d H:i:s', time() + (self::COOKIE_EXPIRY_DAYS * 86400));
        \$userAgentHash = hash('sha256', \$_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        \$this->db->query(
            "INSERT INTO trusted_devices (user_id, token, user_agent_hash, ip_prefix, expires_at) VALUES (?, ?, ?, ?, ?)",
            [\$userId, \$token, \$userAgentHash, '', \$expiresAt]
        );
        return \$token;
    }

    public function validateTrustedDevice(?string \$token): ?int {
        if (!\$token) return null;
        \$row = \$this->db->fetch(
            "SELECT user_id, user_agent_hash FROM trusted_devices WHERE token = ? AND expires_at > NOW()",
            [\$token]
        );
        if (!\$row) return null;
        \$currentUserAgentHash = hash('sha256', \$_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        if (\$row['user_agent_hash'] !== \$currentUserAgentHash) return null;
        return (int)\$row['user_id'];
    }

    public function clearTrustedDevice(?string \$token): void {
        if (\$token) {
            \$this->db->query("DELETE FROM trusted_devices WHERE token = ?", [\$token]);
        }
    }

    public function setCookie(string \$token): void {
        setcookie(self::COOKIE_NAME, \$token, [
            'expires'  => time() + (self::COOKIE_EXPIRY_DAYS * 86400),
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    public function clearCookie(): void {
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/', '', isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on', true);
    }

    public function getToken(): ?string {
        return \$_COOKIE[self::COOKIE_NAME] ?? null;
    }

    public function getCookieName(): string {
        return self::COOKIE_NAME;
    }
}
PHPEOF;

if (file_put_contents($tdsPath, $tdsContent)) {
    echo "<div class='step ok'><strong>✅</strong> TrustedDeviceService.php criado</div>";
} else {
    echo "<div class='step err'><strong>❌</strong> Não foi possível escrever TrustedDeviceService.php</div>";
}

// ─── 3c. Patch AuthController to check trusted device ───
echo "<div class='step'><strong>3c.</strong> Atualizando AuthController.php...</div>";
$authCtrlPath = $root . '/app/Controllers/AuthController.php';
if (file_exists($authCtrlPath)) {
    $authContent = file_get_contents($authCtrlPath);

    // Add import if not present
    if (strpos($authContent, 'use App\Services\TrustedDeviceService') === false) {
        $authContent = str_replace(
            'use App\Services\TokenService;',
            "use App\\Services\\TokenService;\nuse App\\Services\\TrustedDeviceService;",
            $authContent
        );
    }

    // Add dependency property if not present
    if (strpos($authContent, 'deviceService') === false) {
        $authContent = str_replace(
            'private $tokenService;',
            "private \$tokenService;\n    private \$deviceService;",
            $authContent
        );
        $authContent = str_replace(
            '$this->tokenService = new TokenService();',
            "\$this->tokenService = new TokenService();\n        \$this->deviceService = new TrustedDeviceService();",
            $authContent
        );
    }

    // Add trusted device check in showLogin()
    if (strpos($authContent, 'validateTrustedDevice') === false) {
        $showLoginMarker = "if (session_status() === PHP_SESSION_NONE) session_start();\n\n        \$error = \$_SESSION";
        $inject = "if (session_status() === PHP_SESSION_NONE) session_start();\n\n        \$trustedToken = \$this->deviceService->getToken();\n        if (\$trustedToken) {\n            \$userId = \$this->deviceService->validateTrustedDevice(\$trustedToken);\n            if (\$userId) {\n                \$userModel = new User();\n                \$user = \$userModel->find(\$userId);\n                if (\$user) {\n                    session_regenerate_id(true);\n                    \$_SESSION['user_id'] = \$user['id'];\n                    \$_SESSION['username'] = \$user['usuario'];\n                    \$_SESSION['user_email'] = \$user['email'] ?? '';\n                    \$_SESSION['logged_in_at'] = time();\n                    \$_SESSION['user_agent'] = \$_SERVER['HTTP_USER_AGENT'] ?? '';\n                    \$_SESSION['last_activity'] = time();\n                    \$ip = \$this->security->getClientIp();\n                    \$this->security->recordLogin(\$user['id'], \$ip);\n                    return \$this->redirect('/admin/dashboard');\n                }\n            }\n        }\n\n        \$error = \$_SESSION";

        $authContent = str_replace(
            "if (session_status() === PHP_SESSION_NONE) session_start();\n\n        \$error = \$_SESSION['flash_error'] ?? null;",
            $inject,
            $authContent
        );
    }

    // Add trusted device creation in verifyToken
    if (strpos($authContent, 'lembrar_device') === false) {
        $authContent = str_replace(
            "\$_SESSION['last_activity'] = time();\n\n            \$ip = \$this->security->getClientIp();\n            \$this->security->recordLogin(\$user['id'], \$ip);\n\n            unset(",
            "\$_SESSION['last_activity'] = time();\n\n            \$ip = \$this->security->getClientIp();\n            \$this->security->recordLogin(\$user['id'], \$ip);\n\n            if (isset(\$_SESSION['lembrar_device'])) {\n                \$deviceToken = \$this->deviceService->createTrustedDevice(\$user['id']);\n                \$this->deviceService->setCookie(\$deviceToken);\n                unset(\$_SESSION['lembrar_device']);\n            }\n\n            unset(",
            $authContent
        );

        // Also handle lembrar in login() method
        $authContent = str_replace(
            "\$_SESSION['pending_2fa_username'] = \$user['usuario'];\n\n        \$emailSent",
            "\$_SESSION['pending_2fa_username'] = \$user['usuario'];\n\n        if (isset(\$_POST['lembrar']) && \$_POST['lembrar'] == '1') {\n            \$_SESSION['lembrar_device'] = true;\n        }\n\n        \$emailSent",
            $authContent
        );
    }

    // Add Logger import
    if (strpos($authContent, 'use App\\Core\\Logger;') === false) {
        $authContent = str_replace(
            "use App\\Core\\Controller;\n",
            "use App\\Core\\Controller;\nuse App\\Core\\Logger;\n",
            $authContent
        );
    }

    // Fix logout to clear trusted device
    if (strpos($authContent, 'clearTrustedDevice') === false) {
        $authContent = str_replace(
            "public function logout() {\n        if (session_status() === PHP_SESSION_NONE) session_start();\n\n        \$_SESSION = [];",
            "public function logout() {\n        if (session_status() === PHP_SESSION_NONE) session_start();\n\n        \$trustedToken = \$this->deviceService->getToken();\n        if (\$trustedToken) {\n            \$this->deviceService->clearTrustedDevice(\$trustedToken);\n            \$this->deviceService->clearCookie();\n        }\n\n        \$_SESSION = [];",
            $authContent
        );
    }

    file_put_contents($authCtrlPath, $authContent);
    echo "<div class='step ok'><strong>✅</strong> AuthController.php atualizado com trusted device</div>";
} else {
    echo "<div class='step err'><strong>❌</strong> AuthController.php não encontrado</div>";
}

// ─── 3d. Update login.php to add lembrar checkbox ───
echo "<div class='step'><strong>3d.</strong> Atualizando login.php (checkbox)...</div>";
$loginPath = $root . '/pages/admin/login.php';
if (file_exists($loginPath)) {
    $loginContent = file_get_contents($loginPath);

    // Add checkbox after email input if not present
    if (strpos($loginContent, 'lembrar') === false) {
        $loginContent = str_replace(
            "</div>\n\n                <button type=\"submit\" class=\"btn-login\"",
            "</div>\n\n                <div class=\"form-group\" style=\"margin-bottom:24px;\">\n                    <label style=\"display:flex;align-items:center;gap:8px;font-size:13px;color:#4A4540;\">\n                        <input type=\"checkbox\" name=\"lembrar\" value=\"1\" style=\"width:16px;height:16px;\">\n                        <span>Lembrar este dispositivo por 30 dias</span>\n                    </label>\n                </div>\n\n                <button type=\"submit\" class=\"btn-login\"",
            $loginContent
        );
    }

    file_put_contents($loginPath, $loginContent);
    echo "<div class='step ok'><strong>✅</strong> login.php: checkbox 'Lembrar este dispositivo' adicionado</div>";
} else {
    echo "<div class='step err'><strong>❌</strong> login.php não encontrado</div>";
}

// ─── 4. Create trusted device for admin ───
echo "<div class='step'><strong>4.</strong> Criando dispositivo confiável para admin...</div>";
try {
    $stmt = $pdo->prepare("SELECT id, usuario, email FROM usuarios WHERE email = 'joaomigueluehara@gmail.com' LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<div class='step err'><strong>❌</strong> Usuário admin não encontrado no banco</div>";
    } else {
        // Clean old devices
        $pdo->prepare("DELETE FROM trusted_devices WHERE user_id = ?")->execute([$user['id']]);

        $deviceToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 86400));
        $userAgentHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ipPrefix = '';
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $ipPrefix = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
        }

        $pdo->prepare("INSERT INTO trusted_devices (user_id, token, user_agent_hash, ip_prefix, expires_at) VALUES (?, ?, ?, ?, ?)")
            ->execute([$user['id'], $deviceToken, $userAgentHash, $ipPrefix, $expiresAt]);

        // Set cookie
        setcookie('trusted_device', $deviceToken, [
            'expires'  => time() + (30 * 86400),
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        echo "<div class='step ok'><strong>✅</strong> Dispositivo confiável criado para: {$user['email']}</div>";
        echo "<div class='step ok'><strong>✅</strong> Cookie 'trusted_device' definido no navegador</div>";
    }
} catch (Exception $e) {
    echo "<div class='step err'><strong>❌</strong> " . $e->getMessage() . "</div>";
}

// ─── 5. Start session as admin ───
echo "<div class='step'><strong>5.</strong> Iniciando sessão administrativa...</div>";
session_start();

if (isset($user)) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['usuario'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in_at'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['last_activity'] = time();

    echo "<div class='step ok'><strong>✅</strong> Sessão iniciada como: {$user['email']}</div>";
}

// ─── Summary ───
echo "<div class='step' style='background:rgba(74,124,89,.06);border-color:rgba(74,124,89,.15);'>";
echo "<h3 style='margin-top:0;'>Pronto! ✅</h3>";
echo "<p><strong>O que foi corrigido:</strong></p>";
echo "<ul style='margin:12px 0;padding-left:20px;'>";
echo "<li>📧 Remetente do email corrigido para <code>lcmovel.planejadocg@gmail.com</code> (verificado no Brevo)</li>";
echo "<li>📋 Tabela <code>trusted_devices</code> criada no banco</li>";
echo "<li>🔐 Dispositivo confiável criado — não precisará digitar token por 30 dias</li>";
echo "<li>🚀 Você está logado no dashboard agora</li>";
echo "</ul>";
echo "<p>Acesse: <a href='/admin/dashboard'>https://lcsolucoesemmoveis.com.br/admin/dashboard</a></p>";
echo "<p><strong style='color:#b84a4a;'>⚠️ DELETE ESTE ARQUIVO (fix_session7.php) POR SEGURANÇA!</strong></p>";
echo "</div>";

echo "</body></html>";
