CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(255) NOT NULL DEFAULT '',
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `segmento_origem` varchar(100) DEFAULT NULL COMMENT 'tabela de segmento de onde veio',
  `origem` enum('manual','importado','email','whatsapp','indicacao','site','outro') DEFAULT 'manual',
  `status` enum('lead','contato','projeto','concluido','inativo') DEFAULT 'lead',
  `contato_inicial` date DEFAULT NULL,
  `ultimo_contato` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `contato_inicial` (`contato_inicial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `projetos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `descricao` varchar(500) NOT NULL DEFAULT '',
  `tipo` enum('moveis_planejados','cozinha','dormitorio','sala','escritorio','closet','outro') DEFAULT 'moveis_planejados',
  `valor` decimal(12,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL,
  `status` enum('orcamento','aprovado','producao','instalacao','concluido','cancelado') DEFAULT 'orcamento',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `status` (`status`),
  CONSTRAINT `projetos_cliente_fk` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
