CREATE TABLE IF NOT EXISTS `configurator_material_brands` (
    `id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    `description` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_material_lines` (
    `id` varchar(80) NOT NULL,
    `brand_id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_brand` (`brand_id`),
    CONSTRAINT `fk_line_brand` FOREIGN KEY (`brand_id`) REFERENCES `configurator_material_brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_material_patterns` (
    `id` varchar(100) NOT NULL,
    `line_id` varchar(80) NOT NULL,
    `name` varchar(100) NOT NULL,
    `category` varchar(50),
    `base_color` varchar(7),
    `roughness` decimal(3,2) DEFAULT 0.5,
    `metalness` decimal(3,2) DEFAULT 0.0,
    `texture_url` varchar(255),
    `normal_url` varchar(255),
    `roughness_map_url` varchar(255),
    `preview_url` varchar(255),
    `application_image_url` varchar(255),
    `thickness_mm` int(11),
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    `reference` varchar(100),
    `asset_3d_url` varchar(255),
    `grain_direction` varchar(20) DEFAULT 'vertical',
    `asset_source` varchar(255),
    `asset_license` varchar(255),
    PRIMARY KEY (`id`),
    KEY `idx_line` (`line_id`),
    CONSTRAINT `fk_pattern_line` FOREIGN KEY (`line_id`) REFERENCES `configurator_material_lines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_material_finishes` (
    `id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `slug` varchar(50) NOT NULL,
    `roughness` decimal(3,2) DEFAULT 0.5,
    `metalness` decimal(3,2) DEFAULT 0.0,
    `has_grain` tinyint(1) DEFAULT 0,
    `description` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_material_thicknesses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `thickness_mm` int(11) NOT NULL,
    `label` varchar(20) NOT NULL,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `thickness_mm` (`thickness_mm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_furniture_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key_name` varchar(50) NOT NULL,
    `label` varchar(100) NOT NULL,
    `category` varchar(50) NOT NULL,
    `dimensions_json` text,
    `limits_json` text,
    `defaults_json` text,
    `mounted` tinyint(1) DEFAULT 0,
    `back_thickness_mm` decimal(4,1) DEFAULT 0.8,
    `model_path` varchar(255),
    `description` text,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_hardware_brands` (
    `id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    `description` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_handles` (
    `id` varchar(50) NOT NULL,
    `brand_id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `model_code` varchar(100),
    `material` varchar(50),
    `finish_hex` varchar(7),
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    `description` text,
    `model_path` varchar(255),
    PRIMARY KEY (`id`),
    KEY `idx_brand` (`brand_id`),
    CONSTRAINT `fk_handle_brand` FOREIGN KEY (`brand_id`) REFERENCES `configurator_hardware_brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_led_options` (
    `id` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `available_at_lc` tinyint(1) NOT NULL DEFAULT 0,
    `description` text,
    `model_path` varchar(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_compatibility_rules` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `component_type` varchar(50) NOT NULL,
    `component_id` varchar(100) NOT NULL,
    `hardware_type` varchar(50) NOT NULL,
    `hardware_id` varchar(100) NOT NULL,
    `is_compatible` tinyint(1) NOT NULL DEFAULT 1,
    `conditions` text,
    PRIMARY KEY (`id`),
    KEY `idx_component` (`component_type`, `component_id`),
    KEY `idx_hardware` (`hardware_type`, `hardware_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configurator_projects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11),
    `share_id` varchar(50) UNIQUE,
    `name` varchar(255) NOT NULL DEFAULT 'Projeto sem nome',
    `configuration` longtext NOT NULL,
    `configuration_version` varchar(20) DEFAULT '1.0.0',
    `ip_address` varchar(45),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_share` (`share_id`),
    KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `configurator_material_brands` (`id`, `name`, `available_at_lc`, `description`) VALUES
('generic', 'Cores padrão LC', 1, 'Cores padrão oferecidas pela LC Soluções em Móveis'),
('greenplac', 'Greenplac', 0, 'Fabricante de MDF - aguardando confirmação'),
('duratex', 'Duratex', 0, 'Fabricante de MDF - aguardando confirmação'),
('arauco', 'Arauco', 0, 'Fabricante de MDF - aguardando confirmação'),
('guararapes', 'Guararapes', 0, 'Fabricante de MDF - aguardando confirmação'),
('berneck', 'Berneck', 0, 'Fabricante de MDF - aguardando confirmação'),
('eucatex', 'Eucatex', 0, 'Fabricante de MDF - aguardando confirmação'),
('placas_do_brasil', 'Placas do Brasil', 0, 'Fabricante de MDF - aguardando confirmação'),
('sudati', 'Sudati', 0, 'Fabricante de MDF - aguardando confirmação'),
('floraplac', 'Floraplac / Flora', 0, 'Fabricante de MDF - aguardando confirmação'),
('fibraplac', 'Fibraplac', 0, 'Fabricante de MDF - aguardando confirmação');

INSERT IGNORE INTO `configurator_material_lines` (`id`, `brand_id`, `name`, `available_at_lc`) VALUES
('lc_padrao', 'generic', 'Cores padrão LC', 1),
('greenplac_colore', 'greenplac', 'Colore', 0),
('greenplac_texture', 'greenplac', 'Texture', 0),
('greenplac_essenziale', 'greenplac', 'Essenziale', 0),
('greenplac_matiz', 'greenplac', 'Matiz', 0),
('greenplac_natural', 'greenplac', 'Natural', 0),
('greenplac_decore', 'greenplac', 'Decore', 0),
('greenplac_classicos', 'greenplac', 'Clássicos', 0),
('greenplac_mdf_green', 'greenplac', 'MDF Green', 0),
('duratex_essencial', 'duratex', 'Essencial', 0),
('duratex_design', 'duratex', 'Design', 0),
('duratex_cristallo', 'duratex', 'Cristallo', 0),
('duratex_velluto', 'duratex', 'Velluto', 0),
('duratex_trama', 'duratex', 'Trama', 0),
('duratex_conceito', 'duratex', 'Conceito', 0),
('duratex_acetinatta', 'duratex', 'Acetinatta', 0),
('duratex_you', 'duratex', 'Duratex You', 0),
('arauco_madeiras', 'arauco', 'Madeiras', 0),
('arauco_cores', 'arauco', 'Cores', 0),
('arauco_metais', 'arauco', 'Metais', 0),
('arauco_tecidos', 'arauco', 'Tecidos', 0),
('arauco_pedras', 'arauco', 'Pedras', 0),
('berneck_mdf', 'berneck', 'MDF', 0),
('berneck_mdf_plus', 'berneck', 'MDF PLUS', 0),
('berneck_mdf_bp', 'berneck', 'MDF BP', 0),
('berneck_mdp', 'berneck', 'MDP', 0),
('berneck_hdf', 'berneck', 'HDF', 0),
('eucatex_bp', 'eucatex', 'BP', 0),
('eucatex_lacca', 'eucatex', 'Lacca', 0),
('eucatex_matt', 'eucatex', 'Matt', 0),
('eucatex_grafis', 'eucatex', 'Grafis', 0),
('eucatex_raizes', 'eucatex', 'Raízes', 0);

INSERT IGNORE INTO `configurator_material_finishes` (`id`, `name`, `slug`, `roughness`, `metalness`, `has_grain`, `description`) VALUES
('melamina', 'Melamina', 'melamina', 0.75, 0.0, 0, 'Acabamento em melamina - superfície lisa'),
('texturizado', 'Texturizado (madeira)', 'texturizado', 0.85, 0.0, 1, 'Acabamento texturizado simulando madeira'),
('liso', 'Liso (fosco)', 'liso', 0.55, 0.0, 0, 'Acabamento liso fosco'),
('laca', 'Laca (brilhante)', 'laca', 0.15, 0.12, 0, 'Acabamento em laca brilhante'),
('madeira', 'Madeira natural', 'madeira', 0.80, 0.0, 1, 'Madeira natural sem acabamento'),
('acetinado', 'Acetinado', 'acetinado', 0.45, 0.02, 0, 'Acabamento acetinado semi-brilhante');

INSERT IGNORE INTO `configurator_material_thicknesses` (`id`, `thickness_mm`, `label`, `available_at_lc`) VALUES
(1, 6, '6 mm', 0), (2, 9, '9 mm', 0), (3, 12, '12 mm', 1), (4, 15, '15 mm', 1), (5, 18, '18 mm', 1), (6, 25, '25 mm', 0);

INSERT IGNORE INTO `configurator_hardware_brands` (`id`, `name`, `available_at_lc`, `description`) VALUES
('generic', 'Padrão LC', 1, 'Puxadores e ferragens padrão oferecidas pela LC'),
('blum', 'Blum', 0, 'Aguardando confirmação'),
('hettich', 'Hettich', 0, 'Aguardando confirmação'),
('hafele', 'Häfele', 0, 'Aguardando confirmação'),
('fgvtn', 'FGVTN', 0, 'Aguardando confirmação'),
('bigfer', 'Bigfer', 0, 'Aguardando confirmação'),
('renna', 'Renna', 0, 'Aguardando confirmação'),
('rometal', 'Rometal', 0, 'Aguardando confirmação'),
('ducasse', 'Ducasse', 0, 'Aguardando confirmação');

INSERT IGNORE INTO `configurator_handles` (`id`, `brand_id`, `name`, `model_code`, `material`, `finish_hex`, `available_at_lc`, `description`, `model_path`) VALUES
('alca', 'generic', 'Alça', NULL, 'aluminio', '#1A1714', 1, 'Puxador clássico em alça cilindrada.', NULL),
('botao', 'generic', 'Botão', NULL, 'aluminio', '#1A1714', 1, 'Puxador embutido tipo botão.', NULL),
('cava', 'generic', 'Cava (recesso)', NULL, 'aluminio', '#1A1714', 1, 'Puxador em cava reto com recesso na porta.', NULL),
('perfil', 'generic', 'Perfil', NULL, NULL, NULL, 0, 'Puxador em perfil reto - pendente de especificação.', NULL),
('concha', 'generic', 'Concha', NULL, NULL, NULL, 0, 'Puxador em formato de concha - pendente de especificação.', NULL),
('embutido', 'generic', 'Embutido', NULL, NULL, NULL, 0, 'Puxador totalmente embutido - pendente de especificação.', NULL),
('nenhum', 'generic', 'Nenhum', NULL, NULL, NULL, 1, 'Sem puxador (portas com fechamento automático ou cava puro).', NULL);

INSERT IGNORE INTO `configurator_led_options` (`id`, `name`, `available_at_lc`, `description`, `model_path`) VALUES
('none', 'Sem LED', 1, NULL, NULL),
('fita_interno', 'Fita LED interna', 1, 'Iluminação interna do guarda-roupa', NULL),
('perfil_inferior', 'Perfil inferior', 0, 'Aguardando confirmação', NULL),
('nicho', 'Iluminação de nicho', 0, 'Aguardando confirmação', NULL),
('sensor', 'Com sensor de presença', 0, 'Aguardando confirmação', NULL);

INSERT IGNORE INTO `configurator_furniture_types` (`id`, `key_name`, `label`, `category`, `dimensions_json`, `limits_json`, `defaults_json`, `mounted`, `back_thickness_mm`, `description`, `available_at_lc`) VALUES
(1, 'guarda_roupa', 'Guarda-roupa planejado', 'quarto', '{"W":180,"H":220,"D":55}', '{"minW":40,"maxW":400,"minH":30,"maxH":300,"minD":20,"maxD":80,"maxDoors":8,"maxShelves":10,"maxDrawers":6,"maxDividers":4}', '{"doors":2,"shelves":3,"drawers":0,"dividerCount":0}', 0, 0.8, 'Ideal para quartos: 120-240 cm de largura.', 1),
(2, 'nicho', 'Nicho', 'decorativo', '{"W":80,"H":80,"D":30}', '{"minW":40,"maxW":200,"minH":30,"maxH":240,"minD":15,"maxD":50,"maxDoors":0,"maxShelves":8,"maxDrawers":0,"maxDividers":3}', '{"doors":0,"shelves":3,"drawers":0,"dividerCount":0}', 1, 0.6, 'Suspenso. Altura comum: 40-120 cm.', 1),
(3, 'aereo', 'Móvel Aéreo', 'cozinha', '{"W":120,"H":40,"D":30}', '{"minW":60,"maxW":300,"minH":20,"maxH":90,"minD":20,"maxD":50,"maxDoors":6,"maxShelves":8,"maxDrawers":0,"maxDividers":3}', '{"doors":2,"shelves":2,"drawers":0,"dividerCount":0}', 1, 0.6, 'Suspenso, acima de bancadas ou tanque.', 1),
(4, 'estante', 'Estante', 'sala', '{"W":90,"H":200,"D":35}', '{"minW":60,"maxW":200,"minH":60,"maxH":260,"minD":25,"maxD":50,"maxDoors":0,"maxShelves":12,"maxDrawers":0,"maxDividers":4}', '{"doors":0,"shelves":5,"drawers":0,"dividerCount":0}', 0, 0.8, 'Largura 60-120 cm, altura ate 240 cm.', 1),
(5, 'painel_tv', 'Painel para TV', 'sala', '{"W":180,"H":50,"D":35}', '{"minW":80,"maxW":300,"minH":30,"maxH":120,"minD":25,"maxD":50,"maxDoors":0,"maxShelves":6,"maxDrawers":3,"maxDividers":2}', '{"doors":0,"shelves":2,"drawers":1,"dividerCount":0}', 0, 0.8, 'Altura 40-60 cm para base de TV.', 1),
(6, 'cozinha', 'Armario de Cozinha', 'cozinha', '{"W":120,"H":90,"D":60}', '{"minW":30,"maxW":400,"minH":30,"maxH":240,"minD":50,"maxD":80,"maxDoors":8,"maxShelves":8,"maxDrawers":8,"maxDividers":6}', '{"doors":2,"shelves":2,"drawers":2,"dividerCount":1}', 0, 0.8, 'Bancada padrao: profundidade 60 cm.', 1),
(7, 'closet', 'Closet', 'quarto', '{"W":240,"H":240,"D":60}', '{"minW":120,"maxW":400,"minH":180,"maxH":300,"minD":50,"maxD":80,"maxDoors":12,"maxShelves":12,"maxDrawers":8,"maxDividers":6}', '{"doors":3,"shelves":4,"drawers":2,"dividerCount":2}', 0, 0.8, 'Largura ampla: 200-360 cm.', 1),
(8, 'comoda', 'Comoda', 'quarto', '{"W":120,"H":80,"D":45}', '{"minW":60,"maxW":200,"minH":30,"maxH":120,"minD":35,"maxD":60,"maxDoors":0,"maxShelves":4,"maxDrawers":10,"maxDividers":0}', '{"doors":0,"shelves":0,"drawers":4,"dividerCount":0}', 0, 0.8, 'Altura 70-90 cm, 4-6 gavetas.', 1);

INSERT IGNORE INTO `configurator_compatibility_rules` (`component_type`, `component_id`, `hardware_type`, `hardware_id`, `is_compatible`, `conditions`) VALUES
('door', 'all', 'hinges', 'all', 1, 'door_hinge_side = left|right'),
('drawer', 'all', 'slides', 'all', 1, 'drawer_width >= 20cm and drawer_depth >= 40cm'),
('door', 'all', 'tracks', 'all', 0, 'use sliding_door_system instead'),
('drawer', 'all', 'handles', 'all', 1, 'handle_type in [alca, botao, cava, perfil, concha]');
