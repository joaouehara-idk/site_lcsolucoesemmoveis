<?php

namespace App\Services;

use App\Core\Database;

class LoginSecurityService {
    private $db;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const DELAY_BASE_MS = 500;
    private const HONEYPOT_FIELD = 'fax_number';
    private const TIMING_FIELD = '_ts';
    private const MIN_SUBMIT_SECONDS = 2;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function validateCredentials(array $data): array {
        $errors = [];
        $ip = $this->getClientIp();

        if ($this->isIpBlocked($ip)) {
            $errors[] = 'Muitas tentativas. Tente novamente em alguns minutos.';
            $this->addDelay();
            return $errors;
        }

        if (!empty($data[self::HONEYPOT_FIELD])) {
            $this->logBotAttempt($ip);
            $errors[] = 'Erro de validação.';
            $this->addDelay();
            return $errors;
        }

        $email = trim($data['email'] ?? '');
        $password = $data['senha'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = 'Preencha todos os campos.';
            return $errors;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $errors[] = 'Email inválido.';
            return $errors;
        }

        if (strlen($password) > 255) {
            $errors[] = 'Dados inválidos.';
            return $errors;
        }

        $user = $this->findUserByEmail($email);

        if (!$user || !password_verify($password, $user['senha'])) {
            $this->recordFailedAttempt($user ? $user['id'] : null, $ip);
            $this->addDelay();
            $errors[] = 'Email ou senha inválidos.';
            return $errors;
        }

        if ($this->isAccountLocked($user)) {
            $errors[] = 'Conta temporariamente bloqueada. Aguarde ' . self::LOCKOUT_MINUTES . ' minutos.';
            return $errors;
        }

        $this->resetAttempts($user['id']);

        return ['_user' => $user];
    }

    private function findUserByEmail(string $email) {
        $userModel = new \App\Models\User();
        return $userModel->findByEmail($email);
    }

    private function recordFailedAttempt(?int $userId, string $ip): void {
        if ($userId) {
            $this->db->query(
                "UPDATE usuarios SET tentativas_login = tentativas_login + 1 WHERE id = ?",
                [$userId]
            );
            $user = $this->db->fetch("SELECT tentativas_login FROM usuarios WHERE id = ?", [$userId]);
            if ($user && $user['tentativas_login'] >= self::MAX_ATTEMPTS) {
                $this->lockAccount($userId);
            }
        }
        $this->recordIpAttempt($ip);
    }

    private function lockAccount(int $userId): void {
        $unlockAt = date('Y-m-d H:i:s', time() + (self::LOCKOUT_MINUTES * 60));
        $this->db->query(
            "UPDATE usuarios SET bloqueado_ate = ? WHERE id = ?",
            [$unlockAt, $userId]
        );
    }

    private function isAccountLocked(array $user): bool {
        if (empty($user['bloqueado_ate'])) return false;
        $unlockTime = strtotime($user['bloqueado_ate']);
        if ($unlockTime > time()) return true;
        $this->db->query(
            "UPDATE usuarios SET bloqueado_ate = NULL, tentativas_login = 0 WHERE id = ?",
            [$user['id']]
        );
        return false;
    }

    public function resetAttempts(int $userId): void {
        $this->db->query(
            "UPDATE usuarios SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?",
            [$userId]
        );
    }

    public function recordLogin(int $userId, string $ip): void {
        $this->db->query(
            "UPDATE usuarios SET ultimo_login = NOW(), ultimo_ip = ? WHERE id = ?",
            [$ip, $userId]
        );
    }

    private function isIpBlocked(string $ip): bool {
        try {
            $row = $this->db->fetch(
                "SELECT COUNT(*) as cnt FROM login_attempts WHERE ip = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
                [$ip, self::LOCKOUT_MINUTES]
            );
            return $row && intval($row['cnt']) >= (self::MAX_ATTEMPTS * 3);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function recordIpAttempt(string $ip): void {
        try {
            $this->db->query(
                "INSERT INTO login_attempts (ip, created_at) VALUES (?, NOW())",
                [$ip]
            );
            $this->db->query(
                "DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)"
            );
        } catch (\Throwable $e) {
            // silently ignore if table doesn't exist yet
        }
    }

    private function logBotAttempt(string $ip): void {
        $this->recordIpAttempt($ip);
    }

    public function getClientIp(): string {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = explode(',', $_SERVER[$header])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function addDelay(): void {
        usleep(random_int(self::DELAY_BASE_MS * 1000, self::DELAY_BASE_MS * 2000));
    }

    public function getHoneypotField(): string {
        return self::HONEYPOT_FIELD;
    }

    public function getTimingField(): string {
        return self::TIMING_FIELD;
    }
}
