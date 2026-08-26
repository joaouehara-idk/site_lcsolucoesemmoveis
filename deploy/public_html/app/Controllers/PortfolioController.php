<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Projeto;

class PortfolioController extends Controller {
    public function index() {
        $projetoModel = new Projeto();
        $query = $_GET['q'] ?? '';

        if (!empty($query)) {
            $projetos = $projetoModel->search($query);
        } else {
            $projetos = $projetoModel->all();
        }
        
        // Carregar imagens da galeria para cada projeto
        foreach ($projetos as &$p) {
            $p['galeria'] = $projetoModel->getImages($p['id']);
        }
        
        return $this->render('portfolio', [
            'title' => 'Portfólio de Projetos | LC Soluções em Móveis',
            'description' => 'Conheça nossos trabalhos em cozinhas, quartos e ambientes planejados.',
            'projetos' => $projetos,
            'search_term' => $query
        ]);
    }
}
