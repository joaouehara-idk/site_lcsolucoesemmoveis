<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;

class BlogController extends Controller {
    public function index() {
        $postModel = new Post();
        $query = $_GET['q'] ?? '';
        $categoria = $_GET['categoria'] ?? '';

        if (!empty($query)) {
            $posts = $postModel->search($query);
        } elseif (!empty($categoria)) {
            $posts = $postModel->getByCategoria($categoria);
        } else {
            $posts = $postModel->getAllWithCategory();
        }

        $categorias = $postModel->getCategoriesWithCount();
        
        return $this->render('blog', [
            'title' => 'Blog LC Soluções em Móveis | Dicas e Tendências de Móveis Planejados',
            'description' => 'Dicas, guias, comparações e projetos de móveis planejados em Campo Grande. Conteúdo para ajudar você a planejar seus móveis com qualidade.',
            'posts' => $posts,
            'categorias' => $categorias,
            'search_term' => $query,
            'categoria_atual' => $categoria
        ]);
    }

    public function show($slug) {
        $postModel = new Post();
        $post = $postModel->getBySlug($slug);
        
        if (!$post) {
            $this->redirect("/blog");
        }

        $postModel->incrementViews($post['id']);

        $related = $postModel->getRelated($post['id'], $post['categoria'], 3);

        $imgUrl = '';
        if (!empty($post['imagem'])) {
            $imgUrl = (strpos($post['imagem'], 'http') === 0) ? $post['imagem'] : SITE_URL . $post['imagem'];
        }

        return $this->render('blog_post', [
            'title' => ($post['meta_title'] ?? $post['titulo']) . ' | Blog LC Soluções em Móveis',
            'description' => $post['meta_description'] ?? ($post['resumo'] ?? substr(strip_tags($post['conteudo']), 0, 160)),
            'og_type' => 'article',
            'og_image' => $imgUrl ?: null,
            'breadcrumb_atual' => 'Blog',
            'breadcrumb_url' => SITE_URL . '/blog',
            'post' => $post,
            'related_posts' => $related
        ]);
    }
}
