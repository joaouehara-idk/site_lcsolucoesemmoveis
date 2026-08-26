<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Contato;
use App\Models\User;
use App\Services\AIService;

class AdminController extends Controller {
    private function isMasterAdmin() {
        return isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'joaomigueluehara@gmail.com';
    }

    private function getEmailsAutorizados() {
        $db = \App\Core\Database::getInstance();
        $row = $db->fetch("SELECT valor FROM configuracoes WHERE chave = ?", ['emails_autorizados']);
        if ($row && $row['valor']) {
            return json_decode($row['valor'], true) ?? [];
        }
        return [];
    }

    public function dashboard() {
        try {
            $contatoModel = new Contato();
            $contatos = $contatoModel->all();
        } catch (\Throwable $e) {
            Logger::error('Dashboard contatos error: ' . $e->getMessage());
            $contatos = [];
        }

        try {
            $postModel = new \App\Models\Post();
            $topPosts = $postModel->getTopViewed(5);
        } catch (\Throwable $e) {
            Logger::error('Dashboard topPosts error: ' . $e->getMessage());
            $topPosts = [];
        }

        try {
            $projetoModel = new \App\Models\Projeto();
            $topProjetos = $projetoModel->getTopViewed(5);
        } catch (\Throwable $e) {
            Logger::error('Dashboard topProjetos error: ' . $e->getMessage());
            $topProjetos = [];
        }

        return $this->render('admin.dashboard', [
            'title' => 'Dashboard | Painel Administrativo',
            'contatos' => $contatos,
            'topPosts' => $topPosts,
            'topProjetos' => $topProjetos
        ]);
    }

    public function users() {
        $userModel = new User();
        $users = $userModel->all();

        return $this->render('admin.users.index', [
            'title' => 'Gerenciar Usuários',
            'users' => $users,
            'is_master' => $this->isMasterAdmin()
        ]);
    }

    // --- BLOG MANAGEMENT ---
    public function blog() {
        $postModel = new \App\Models\Post();
        $posts = $postModel->all();
        return $this->render('admin.blog.index', [
            'title' => 'Gerenciar Blog',
            'posts' => $posts
        ]);
    }

    public function createPost() {
        return $this->render('admin.blog.create', [
            'title' => 'Novo Post'
        ]);
    }

    // --- PORTFOLIO MANAGEMENT ---
    public function portfolio() {
        $projetoModel = new \App\Models\Projeto();
        $projetos = $projetoModel->all();
        return $this->render('admin.portfolio.index', [
            'title' => 'Gerenciar Portfólio',
            'projetos' => $projetos
        ]);
    }

    public function createProjeto() {
        return $this->render('admin.portfolio.create', [
            'title' => 'Novo Projeto'
        ]);
    }

    public function storePost() {
        $postModel = new \App\Models\Post();
        $db = \App\Core\Database::getInstance();
        
        $slug = $_POST['slug'];
        $existing = $db->fetch("SELECT id FROM posts WHERE slug = ?", [$slug]);
        if ($existing) {
            $slug .= '-' . rand(100, 999);
        }

        $categoriaNome = $_POST['categoria'] ?? 'Geral';
        $categoriaId = null;
        try {
            $catStmt = $db->fetch("SELECT id FROM categorias WHERE nome = ?", [$categoriaNome]);
            if ($catStmt) {
                $categoriaId = (int)$catStmt['id'];
            }
        } catch (\Exception $e) {
            // Silently fail - categoria_id stays null
        }
        $data = [
            'titulo'           => $_POST['titulo'],
            'slug'             => $slug,
            'categoria'        => $categoriaNome,
            'categoria_id'     => $categoriaId,
            'resumo'           => $_POST['resumo'],
            'conteudo'         => $_POST['conteudo'],
            'status'           => $_POST['status'] ?? 'publicado',
            'meta_title'       => $_POST['titulo'],
            'meta_description' => $_POST['resumo']
        ];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $filename = time() . '_' . $_FILES['imagem']['name'];
            $path = 'assets/img/blog/' . $filename;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], ROOT_PATH . '/' . $path)) {
                $data['imagem'] = '/assets/img/blog/' . $filename;
            }
        }

        $postModel->create($data);
        return $this->redirect('/admin/blog');
    }

    public function storeProjeto() {
        $projetoModel = new \App\Models\Projeto();
        $db = \App\Core\Database::getInstance();
        
        $slug = $_POST['slug'];
        $existing = $db->fetch("SELECT id FROM projetos WHERE slug = ?", [$slug]);
        if ($existing) {
            $slug .= '-' . rand(100, 999);
        }

        $data = [
            'titulo'      => $_POST['titulo'],
            'slug'        => $slug,
            'categoria'   => $_POST['categoria'],
            'descricao'   => $_POST['descricao'],
            'destaque'    => isset($_POST['destaque']) ? 1 : 0
        ];

        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === 0) {
            $uploadDir = ROOT_PATH . '/assets/img/portfolio';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . '_' . $_FILES['imagem_capa']['name'];
            $path = 'assets/img/portfolio/' . $filename;
            if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], ROOT_PATH . '/' . $path)) {
                $data['imagem_capa'] = '/assets/img/portfolio/' . $filename;
            }
        }

        if (isset($_FILES['video_capa']) && $_FILES['video_capa']['error'] === 0) {
            $uploadDir = ROOT_PATH . '/assets/img/portfolio';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['video_capa']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_video.' . $ext;
            $path = 'assets/img/portfolio/' . $filename;
            if (move_uploaded_file($_FILES['video_capa']['tmp_name'], ROOT_PATH . '/' . $path)) {
                $data['video_capa'] = '/assets/img/portfolio/' . $filename;
            }
        }

        $projetoId = $projetoModel->create($data);
        \App\Core\Logger::info("Projeto criado com ID: " . $projetoId);

        if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
            \App\Core\Logger::info("Iniciando upload de galeria. Total de arquivos: " . count($_FILES['galeria']['name']));
            foreach ($_FILES['galeria']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['galeria']['error'][$key] === 0) {
                    $filename = time() . '_' . $key . '_' . $_FILES['galeria']['name'][$key];
                    $path = 'assets/img/portfolio/' . $filename;
                    if (move_uploaded_file($tmpName, ROOT_PATH . '/' . $path)) {
                        \App\Core\Logger::info("Imagem salva: " . $path);
                        $projetoModel->addImage($projetoId, '/assets/img/portfolio/' . $filename);
                    } else {
                        \App\Core\Logger::error("Erro ao mover arquivo: " . $_FILES['galeria']['name'][$key]);
                    }
                } else {
                    \App\Core\Logger::error("Erro no upload do arquivo " . $key . ": Código " . $_FILES['galeria']['error'][$key]);
                }
            }
        } else {
            \App\Core\Logger::info("Nenhuma imagem de galeria enviada.");
        }

        return $this->redirect('/admin/portfolio');
    }

    public function createUser() {
        if (!$this->isMasterAdmin()) {
            return $this->redirect('/admin/usuarios');
        }
        return $this->render('admin.users.create', [
            'title' => 'Novo Usuário'
        ]);
    }

    public function storeUser() {
        if (!$this->isMasterAdmin()) {
            return $this->redirect('/admin/usuarios');
        }
        // Gera um código de verificação de 6 dígitos
        $codigo = rand(100000, 999999);
        
        // Dados do novo usuário salvos temporariamente na sessão
        $_SESSION['pending_user'] = [
            'usuario' => $_POST['usuario'],
            'senha'   => password_hash($_POST['senha'], PASSWORD_DEFAULT),
            'email'    => $_POST['email'],
            'codigo'   => $codigo
        ];

        // Tenta enviar o e-mail de autorização para o DONO do site
        $to = CONTACT_EMAIL; // Sempre envia para o e-mail definido no config.php
        $subject = "AUTORIZACAO NECESSARIA - Novo Usuario LC Solucoes";
        $message = "Um novo usuario (" . $_POST['usuario'] . ") esta sendo criado no painel.\n";
        $message .= "Se voce autoriza esta operacao, forneça o codigo abaixo:\n\n";
        $message .= "CODIGO: " . $codigo;
        $headers = "From: sistema@lcsolucoesemmoveis.com";

        @mail($to, $subject, $message, $headers);

        return $this->redirect('/admin/usuarios/verificar');
    }

    public function showVerify() {
        if (!isset($_SESSION['pending_user'])) {
            return $this->redirect('/admin/usuarios/novo');
        }
        
        return $this->render('admin.users.verify', [
            'title' => 'Verificar E-mail',
            'email' => $_SESSION['pending_user']['email']
        ]);
    }

    public function confirmUser() {
        if (!$this->isMasterAdmin()) {
            return $this->redirect('/admin/usuarios');
        }
        if (!isset($_SESSION['pending_user'])) {
            return $this->redirect('/admin/usuarios/novo');
        }

        $inputCode = $_POST['codigo'];
        $pending = $_SESSION['pending_user'];

        if ($inputCode == $pending['codigo']) {
            $userModel = new User();
            
            // Verifica se o usuário já existe para evitar erro de integridade
            if ($userModel->findByUsername($pending['usuario'])) {
                return $this->render('admin.users.verify', [
                    'title' => 'Verificar E-mail',
                    'email' => $pending['email'],
                    'error' => 'Este usuário já foi cadastrado enquanto você verificava o código.'
                ]);
            }

            $data = [
                'username' => $pending['usuario'],
                'password' => $pending['senha'], // Já está hasheado
                'email'    => $pending['email']
            ];
            
            // Ajustando para o modelo User que já hasheia internamente no create se não tomarmos cuidado
            // Vamos garantir que o User model use o que passamos
            $userModel->createWithHashedPassword($data);
            
            unset($_SESSION['pending_user']);
            return $this->redirect('/admin/usuarios');
        } else {
            return $this->render('admin.users.verify', [
                'title' => 'Verificar E-mail',
                'email' => $pending['email'],
                'error' => 'Código incorreto. Tente novamente.'
            ]);
        }
    }

    public function editUser($id) {
        if (!$this->isMasterAdmin() && $_SESSION['user_id'] != $id) {
            return $this->redirect('/admin/usuarios');
        }
        $userModel = new User();
        $user = $userModel->find($id);
        
        return $this->render('admin.users.edit', [
            'title' => 'Editar Usuário',
            'user' => $user
        ]);
    }

    public function updateUser() {
        $id = $_POST['id'];
        if (!$this->isMasterAdmin() && $_SESSION['user_id'] != $id) {
            return $this->redirect('/admin/usuarios');
        }
        $userModel = new User();
        
        $data = [
            'usuario' => $_POST['usuario'],
            'email'   => $_POST['email']
        ];

        // Só atualiza a senha se ela for preenchida
        if (!empty($_POST['senha'])) {
            $data['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);
        return $this->redirect('/admin/usuarios');
    }

    public function deleteUser() {
        if (!$this->isMasterAdmin()) {
            return $this->redirect('/admin/usuarios');
        }
        $userModel = new User();
        $userModel->delete($_POST['id']);
        return $this->redirect('/admin/usuarios');
    }

    // --- CONTACT MESSAGES MANAGEMENT ---
    public function contatos() {
        $contatoModel = new Contato();
        $contatos = $contatoModel->allOrdered();

        return $this->render('admin.contatos', [
            'title' => 'Contatos | Painel Administrativo',
            'contatos' => $contatos
        ]);
    }

    public function deletarContato() {
        $contatoModel = new Contato();
        $contatoModel->delete($_POST['id'] ?? 0);
        return $this->redirect('/admin/contatos');
    }

    // --- BLOG ACTIONS ---
    public function editPost($id) {
        $postModel = new \App\Models\Post();
        $post = $postModel->find($id);
        return $this->render('admin.blog.edit', [
            'title' => 'Editar Post',
            'post' => $post
        ]);
    }

    public function updatePost() {
        $postModel = new \App\Models\Post();
        $id = $_POST['id'];
        
        $categoriaNome = $_POST['categoria'] ?? 'Geral';
        $db = \App\Core\Database::getInstance();
        $catRow = $db->fetch("SELECT id FROM categorias WHERE nome = ?", [$categoriaNome]);
        $data = [
            'titulo'           => $_POST['titulo'],
            'slug'             => $_POST['slug'],
            'categoria'        => $categoriaNome,
            'categoria_id'     => $catRow ? (int)$catRow['id'] : null,
            'resumo'           => $_POST['resumo'],
            'conteudo'         => $_POST['conteudo'],
            'status'           => $_POST['status'],
            'meta_title'       => $_POST['titulo'],
            'meta_description' => $_POST['resumo']
        ];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $filename = time() . '_' . $_FILES['imagem']['name'];
            $path = 'assets/img/blog/' . $filename;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], ROOT_PATH . '/' . $path)) {
                $data['imagem'] = '/assets/img/blog/' . $filename;
            }
        }

        $postModel->update($id, $data);
        return $this->redirect('/admin/blog');
    }

    public function deletePost() {
        $postModel = new \App\Models\Post();
        $postModel->delete($_POST['id']);
        return $this->redirect('/admin/blog');
    }

    // --- PORTFOLIO ACTIONS ---
    public function editProjeto($id) {
        $projetoModel = new \App\Models\Projeto();
        $projeto = $projetoModel->findWithImages($id);
        return $this->render('admin.portfolio.edit', [
            'title' => 'Editar Projeto',
            'projeto' => $projeto
        ]);
    }

    public function updateProjeto() {
        $projetoModel = new \App\Models\Projeto();
        $id = $_POST['id'];
        
        $data = [
            'titulo'    => $_POST['titulo'],
            'slug'      => $_POST['slug'],
            'categoria' => $_POST['categoria'],
            'descricao' => $_POST['descricao'],
            'destaque'  => isset($_POST['destaque']) ? 1 : 0
        ];

        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === 0) {
            $uploadDir = ROOT_PATH . '/assets/img/portfolio';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $path = 'assets/img/portfolio/' . time() . '_' . $_FILES['imagem_capa']['name'];
            move_uploaded_file($_FILES['imagem_capa']['tmp_name'], ROOT_PATH . '/' . $path);
            $data['imagem_capa'] = '/' . $path;
        }

        if (isset($_FILES['video_capa']) && $_FILES['video_capa']['error'] === 0) {
            $uploadDir = ROOT_PATH . '/assets/img/portfolio';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['video_capa']['name'], PATHINFO_EXTENSION);
            $path = 'assets/img/portfolio/' . time() . '_video.' . $ext;
            move_uploaded_file($_FILES['video_capa']['tmp_name'], ROOT_PATH . '/' . $path);
            $data['video_capa'] = '/' . $path;
        }

        $projetoModel->update($id, $data);

        if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
            foreach ($_FILES['galeria']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['galeria']['error'][$key] === 0) {
                    $filename = time() . '_' . $key . '_' . $_FILES['galeria']['name'][$key];
                    $path = 'assets/img/portfolio/' . $filename;
                    if (move_uploaded_file($tmpName, ROOT_PATH . '/' . $path)) {
                        $projetoModel->addImage($id, '/assets/img/portfolio/' . $filename);
                    }
                }
            }
        }

        return $this->redirect('/admin/portfolio');
    }

    public function deleteImagemProjeto() {
        $id = $_POST['imagem_id'];
        $projetoId = $_POST['projeto_id'];
        $db = \App\Core\Database::getInstance();
        
        $imagem = $db->fetch("SELECT caminho FROM projeto_imagens WHERE id = ?", [$id]);
        if ($imagem) {
            $fullPath = ROOT_PATH . $imagem['caminho'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $db->query("DELETE FROM projeto_imagens WHERE id = ?", [$id]);
        }
        
        return $this->redirect('/admin/portfolio/editar/' . $projetoId);
    }

    public function deleteProjeto() {
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            return $this->redirect('/admin/portfolio');
        }

        $db = \App\Core\Database::getInstance();

        $projeto = $db->fetch("SELECT imagem_capa, video_capa FROM projetos WHERE id = ?", [$id]);
        if ($projeto) {
            if (!empty($projeto['imagem_capa'])) {
                $fullPath = ROOT_PATH . $projeto['imagem_capa'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            if (!empty($projeto['video_capa'])) {
                $fullPath = ROOT_PATH . $projeto['video_capa'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $imagens = $db->fetchAll("SELECT caminho FROM projeto_imagens WHERE projeto_id = ?", [$id]);
            foreach ($imagens as $img) {
                $fullPath = ROOT_PATH . $img['caminho'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $db->query("DELETE FROM projetos WHERE id = ?", [$id]);
        return $this->redirect('/admin/portfolio');
    }

    public function aiGeneratePost() {
        $topic = $_POST['topic'] ?? '';
        if (empty($topic)) {
            return $this->json(['error' => 'Por favor, informe um tema ou tópico.']);
        }

        $aiService = new AIService();
        $result = $aiService->generateBlogPost($topic);
        
        return $this->json($result);
    }

    public function aiGeneratePortfolio() {
        $topic = $_POST['topic'] ?? '';
        if (empty($topic)) {
            return $this->json(['error' => 'Por favor, informe o tipo de projeto (ex: Cozinha Moderna em MDF Grafite).']);
        }

        $aiService = new AIService();
        $result = $aiService->generatePortfolioInfo($topic);
        
        return $this->json($result);
    }

    public function emailsAutorizados() {
        if (!$this->isMasterAdmin()) {
            return $this->redirect('/admin/dashboard');
        }
        $emails = $this->getEmailsAutorizados();
        return $this->render('admin.emails_autorizados', [
            'title' => 'Emails Autorizados',
            'emails' => $emails
        ]);
    }

    public function salvarEmailsAutorizados() {
        if (!$this->isMasterAdmin()) {
            return $this->json(['error' => 'Sem permissao'], 403);
        }
        $emails = $_POST['emails'] ?? [];
        if (!is_array($emails)) $emails = [];
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails, function($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); });
        $emails = array_values(array_unique($emails));
        $db = \App\Core\Database::getInstance();
        $db->query("INSERT INTO configuracoes (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?", ['emails_autorizados', json_encode($emails), json_encode($emails)]);
        return $this->redirect('/admin/emails-autorizados');
    }
}
