<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;

class TrustedDeviceService {
    private $db;
    private const COOKIE_NAME = 'trusted_device';
    private const TOKEN_LENGTH = 64;
    private const COOKIE_EXPIRY_DAYS = 30;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createTrustedDevice(int $userId): string {
        $this->clearUserDevices($userId);

        $token = bin2hex(random_bytes(self::TOKEN_LENGTH / 2));
        $expiresAt = gmdate('Y-m-d H:i:s', time() + (self::COOKIE_EXPIRY_DAYS * 86400));

        $userAgentHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $ipPrefix = $this->getIpPrefix($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        $this->db->query(
            "INSERT INTO trusted_devices (user_id, token, user_agent_hash, ip_prefix, expires_at) VALUES (?, ?, ?, ?, ?)",
            [$userId, $token, $userAgentHash, $ipPrefix, $expiresAt]
        );

        try { Logger::info("Trusted device created for user_id: {$userId}"); } catch (\Throwable $e) {}
        return $token;
    }

    public function validateTrustedDevice(?string $token): ?int {
        if (!$token) return null;

        try {
            $row = $this->db->fetch(
                "SELECT user_id, user_agent_hash FROM trusted_devices WHERE token = ? AND expires_at > NOW()",
                [$token]
            );
        } catch (\Throwable $e) {
            // DB query failed — treat as invalid token
            return null;
        }

        if (!$row) return null;

        $currentUserAgentHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

        if ($row['user_agent_hash'] !== $currentUserAgentHash) {
            try { Logger::warning("Trusted device rejected: user agent mismatch for user_id: {$row['user_id']}"); } catch (\Throwable $e) {}
            return null;
        }

        return (int)$row['user_id'];
    }

    public function clearTrustedDevice(?string $token): void {
        if ($token) {
            try {
                $this->db->query("DELETE FROM trusted_devices WHERE token = ?", [$token]);
            } catch (\Throwable $e) {
                // Non-critical
            }
        }
    }

    public function clearUserDevices(int $userId): void {
        try {
            $this->db->query("DELETE FROM trusted_devices WHERE user_id = ?", [$userId]);
        } catch (\Throwable $e) {
            // Non-critical
        }
    }

    public function cleanupExpired(): void {
        try {
            $this->db->query("DELETE FROM trusted_devices WHERE expires_at < NOW()");
        } catch (\Throwable $e) {
            // Non-critical
        }
    }

    public function setCookie(string $token): void {
        $expiry = time() + (self::COOKIE_EXPIRY_DAYS * 86400);
        setcookie(self::COOKIE_NAME, $token, [
            'expires'  => $expiry,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    public function clearCookie(): void {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            setcookie(self::COOKIE_NAME, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }

    public function getToken(): ?string {
        return $_COOKIE[self::COOKIE_NAME] ?? null;
    }

    public function getCookieName(): string {
        return self::COOKIE_NAME;
    }

    private function getIpPrefix(string $ip): string {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return $parts[0] . '.' . $parts[1] . '.' . $parts[2];
        }
        return $ip;
    }
}
