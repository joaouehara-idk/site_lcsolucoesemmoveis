<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Projeto;

class PortfolioController extends Controller {
    public function index() {
        $projetoModel = new Projeto();
        $query = $_GET['q'] ?? '';
        $categoria = $_GET['categoria'] ?? '';

        if (!empty($query)) {
            $projetos = $projetoModel->search($query);
        } elseif (!empty($categoria)) {
            $projetos = $projetoModel->getByCategory($categoria);
        } else {
            $projetos = $projetoModel->all();
        }
        
        foreach ($projetos as &$p) {
            $p['galeria'] = $projetoModel->getImages($p['id']);
        }
        
        // Pegar categorias únicas
        $allProjects = $projetoModel->all();
        $categorias = [];
        foreach ($allProjects as $p) {
            $cat = $p['categoria'] ?? '';
            if (!empty($cat) && !in_array($cat, $categorias)) {
                $categorias[] = $cat;
            }
        }
        sort($categorias);
        
        return $this->render('portfolio', [
            'title' => 'Portfólio de Projetos | LC Soluções em Móveis',
            'description' => 'Conheça nossos trabalhos em cozinhas, quartos e ambientes planejados.',
            'projetos' => $projetos,
            'search_term' => $query,
            'categorias' => $categorias,
            'categoria_atual' => $categoria
        ]);
    }
}
