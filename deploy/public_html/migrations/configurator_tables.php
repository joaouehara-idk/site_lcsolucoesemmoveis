<?php
/**
 * Migration: Create saved_projects table for the 3D Configurator
 */

require_once __DIR__ . '/../app/Core/Database.php';

function runConfiguratorMigration() {
    try {
        $db = \App\Core\Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS `saved_projects` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `project_id` varchar(20) NOT NULL,
                `furniture_type` varchar(50) NOT NULL,
                `configuration_json` longtext NOT NULL,
                `customer_name` varchar(150) DEFAULT NULL,
                `customer_phone` varchar(20) DEFAULT NULL,
                `customer_email` varchar(150) DEFAULT NULL,
                `status` enum('saved','contacted','quoted','ordered','archived') DEFAULT 'saved',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `project_id` (`project_id`),
                KEY `idx_furniture_type` (`furniture_type`),
                KEY `idx_status` (`status`),
                KEY `idx_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (\Exception $e) {
        // Silently fail - table might already exist
    }

    // Future catalog tables (created but populated later with real data)
    try {
        $db = \App\Core\Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS `material_brands` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `slug` varchar(100) NOT NULL,
                `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
                `description` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (\Exception $e) {}

    try {
        $db = \App\Core\Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS `material_patterns` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `brand_id` int(11) DEFAULT NULL,
                `line` varchar(100) DEFAULT NULL,
                `name` varchar(150) NOT NULL,
                `color_hex` varchar(7) DEFAULT NULL,
                `finish_type` varchar(50) DEFAULT NULL,
                `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
                `texture_image` varchar(255) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_brand` (`brand_id`),
                KEY `idx_available` (`available_at_lc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (\Exception $e) {}

    try {
        $db = \App\Core\Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS `hardware_products` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `brand` varchar(100) DEFAULT NULL,
                `category` varchar(50) NOT NULL,
                `name` varchar(150) NOT NULL,
                `model` varchar(100) DEFAULT NULL,
                `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
                `specifications` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_category` (`category`),
                KEY `idx_available` (`available_at_lc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (\Exception $e) {}
}

runConfiguratorMigration();
