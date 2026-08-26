<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;

class TokenService {
    private $db;
    private const TOKEN_LENGTH = 6;
    private const TOKEN_EXPIRY_MINUTES = 10;
    private const MAX_TOKENS_PER_USER = 3;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function generateToken(int $userId): string {
        $this->invalidateAllUserTokens($userId);

        $token = str_pad(random_int(100000, 999999), self::TOKEN_LENGTH, '0', STR_PAD_LEFT);
        $expiresAt = gmdate('Y-m-d H:i:s', time() + (self::TOKEN_EXPIRY_MINUTES * 60));

        $this->db->query(
            "INSERT INTO login_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$userId, $token, $expiresAt]
        );

        $this->cleanupOldTokens();

        return $token;
    }

    public function validateToken(int $userId, string $token): bool {
        $token = trim($token);

        if (strlen($token) !== self::TOKEN_LENGTH || !ctype_digit($token)) {
            return false;
        }

        $row = $this->db->fetch(
            "SELECT id FROM login_tokens WHERE user_id = ? AND token = ? AND used = 0 AND expires_at > NOW()",
            [$userId, $token]
        );

        if (!$row) {
            return false;
        }

        $this->db->query(
            "UPDATE login_tokens SET used = 1 WHERE id = ?",
            [$row['id']]
        );

        return true;
    }

    public function invalidateAllUserTokens(int $userId): void {
        $this->db->query(
            "UPDATE login_tokens SET used = 1 WHERE user_id = ? AND used = 0",
            [$userId]
        );
    }

    public function sendTokenEmail(string $email, string $token, string $userName): bool {
        $siteName = 'LC Soluções em Móveis';
        $subject = "{$siteName} - Código de Verificação";
        $expiry = self::TOKEN_EXPIRY_MINUTES;

        $html = $this->buildEmailHtml($token, $userName, $expiry, $siteName);
        $plainText = "Olá {$userName},\n\nSeu código de verificação é: {$token}\n\nEste código expira em {$expiry} minutos.\n\nSe você não solicitou este código, ignore este e-mail.\n\n{$siteName}";

        $emailService = new EmailService();
        $sent = $emailService->send($email, $subject, $html, $plainText);
        if ($sent) return true;

        Logger::error("Todos os métodos de envio falharam para: {$email}");
        return false;
    }

    private function buildEmailHtml(string $token, string $userName, int $expiry, string $siteName): string {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0a0a0a;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:40px 20px;">
<tr><td align="center">
<table width="400" cellpadding="0" cellspacing="0" style="background:#111;border:1px solid rgba(255,255,255,0.1);border-radius:16px;overflow:hidden;">
<tr><td style="background:#1A1714;padding:30px;text-align:center;">
<h1 style="margin:0;color:#F6F1EB;font-size:24px;font-weight:800;">{$siteName}</h1>
<p style="margin:5px 0 0;color:rgba(255,255,255,0.5);font-size:13px;">Verificação de Identidade</p>
</td></tr>
<tr><td style="padding:40px 30px;text-align:center;">
<p style="color:#aaa;font-size:14px;margin:0 0 10px;">Olá, <strong style="color:#e0ddd5;">{$userName}</strong></p>
<p style="color:#888;font-size:13px;margin:0 0 30px;">Use o código abaixo para acessar o painel:</p>
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">
<div style="background:rgba(255,255,255,0.05);border:2px dashed #1A1714;border-radius:12px;padding:20px 30px;display:inline-block;">
<span style="font-size:36px;font-weight:900;color:#FFFFFF;letter-spacing:12px;font-family:'Courier New',monospace;">{$token}</span>
</div>
</td></tr></table>
<p style="color:#666;font-size:12px;margin:25px 0 0;">Este código expira em <strong style="color:#1A1714;">{$expiry} minutos</strong>.</p>
<p style="color:#555;font-size:11px;margin:15px 0 0;">Se você não solicitou este código, ignore este e-mail.</p>
</td></tr>
<tr><td style="background:rgba(255,255,255,0.02);padding:20px 30px;text-align:center;border-top:1px solid rgba(255,255,255,0.05);">
<p style="color:#555;font-size:11px;margin:0;">Este é um e-mail automático. Não responda.</p>
</td></tr>
</table>
</td></tr></table>
</body></html>
HTML;
    }

    private function cleanupOldTokens(): void {
        $this->db->query(
            "DELETE FROM login_tokens WHERE expires_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
    }
}
