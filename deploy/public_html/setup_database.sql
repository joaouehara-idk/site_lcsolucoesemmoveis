-- ==============================
-- TABELA DE CATEGORIAS (BLOG)
-- ==============================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE POSTS (BLOG - SEO)
-- ==============================
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    conteudo LONGTEXT NOT NULL,
    resumo TEXT,
    imagem VARCHAR(255),
    categoria_id INT,
    meta_title VARCHAR(255),
    meta_description VARCHAR(255),
    status ENUM('publicado', 'rascunho') DEFAULT 'publicado',
    visualizacoes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE CONTATOS (LEADS)
-- ==============================
CREATE TABLE IF NOT EXISTS contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    assunto VARCHAR(255),
    mensagem TEXT NOT NULL,
    ip VARCHAR(45),
    status ENUM('novo', 'lido', 'respondido', 'arquivado') DEFAULT 'novo',
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE PROJETOS (PORTFÓLIO)
-- ==============================
CREATE TABLE IF NOT EXISTS projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    descricao TEXT,
    imagem_capa VARCHAR(255),
    video_capa VARCHAR(255),
    categoria VARCHAR(100),
    destaque BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE IMAGENS DO PROJETO (GALERIA)
-- ==============================
CREATE TABLE IF NOT EXISTS projeto_imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    ordem INT DEFAULT 0,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE PÁGINAS (SEO DINÂMICO)
-- ==============================
CREATE TABLE IF NOT EXISTS paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    conteudo LONGTEXT NOT NULL,
    meta_title VARCHAR(255),
    meta_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE USUÁRIOS (ADMIN)
-- ==============================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(150) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- TABELA DE CONFIGURAÇÕES DO SITE
-- ==============================
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE,
    valor TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================
-- INSERIR DADOS INICIAIS
-- ==============================
INSERT IGNORE INTO categorias (id, nome, slug) VALUES
(1, 'Dicas', 'dicas'),
(2, 'Cozinhas', 'cozinhas'),
(3, 'Dormitórios', 'dormitorios');

INSERT IGNORE INTO configuracoes (chave, valor) VALUES
('site_nome', 'LC Soluções em Móveis'),
('cidade', 'Campo Grande'),
('estado', 'MS');

-- ==============================
-- EXEMPLO DE POST (SEO)
-- ==============================
INSERT IGNORE INTO posts (titulo, slug, conteudo, resumo, categoria_id, meta_title, meta_description) VALUES
('Cozinha Planejada Pequena: 10 Ideias Inteligentes',
 'cozinha-planejada-pequena',
 '<p>Os móveis planejados são a melhor solução para cozinhas pequenas...</p>',
 'Veja como aproveitar cada centímetro da sua cozinha com móveis sob medida.',
 1,
 'Cozinha Planejada Pequena em Campo Grande',
 'Veja ideias incríveis de cozinha planejada pequena em Campo Grande MS.');

-- ==============================
-- EXEMPLO DE PROJETOS (PORTFÓLIO)
-- ==============================
INSERT IGNORE INTO projetos (titulo, slug, descricao, imagem_capa, categoria) VALUES
('Cozinha Gourmet', 'cozinha-gourmet', 'Design moderno com acabamento em MDF de alto padrão e ferragens premium.', '/assets/img/cozinha/cozinha1.jpg', 'Cozinhas'),
('Cozinha Integrada', 'cozinha-integrada', 'Solução inteligente para espaços integrados, unindo praticidade e elegância.', '/assets/img/cozinha/cozinha2.jpg', 'Cozinhas'),
('Suíte Master', 'suite-master', 'Ambiente relaxante com roupeiros planejados e painel de cabeceira exclusivo.', '/assets/img/quarto/quarto1.jpg', 'Dormitórios'),
('Quarto de Solteiro', 'quarto-solteiro', 'Otimização máxima de espaço com móveis multifuncionais.', '/assets/img/quarto/quarto2.jpg', 'Dormitórios');
