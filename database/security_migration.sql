-- Security Migration: Add brute-force protection columns to usuarios
-- Run this on the database to enable login security features

ALTER TABLE `usuarios`
    ADD COLUMN `tentativas_login` INT(11) NOT NULL DEFAULT 0 AFTER `senha`,
    ADD COLUMN `bloqueado_ate` DATETIME DEFAULT NULL AFTER `tentativas_login`,
    ADD COLUMN `ultimo_login` DATETIME DEFAULT NULL AFTER `bloqueado_ate`,
    ADD COLUMN `ultimo_ip` VARCHAR(45) DEFAULT NULL AFTER `ultimo_login`,
    ADD COLUMN `token_reset` VARCHAR(64) DEFAULT NULL AFTER `ultimo_ip`,
    ADD COLUMN `token_reset_expira` DATETIME DEFAULT NULL AFTER `token_reset`;

-- Rate limiting table for IP-based brute-force protection
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(45) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_ip_time` (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login tokens for 2FA email verification
CREATE TABLE IF NOT EXISTS `login_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `token` varchar(6) NOT NULL,
    `expires_at` datetime NOT NULL,
    `used` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_token` (`user_id`, `token`, `used`),
    KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
