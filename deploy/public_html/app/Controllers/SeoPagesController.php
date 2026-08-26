<?php

namespace App\Controllers;

use App\Core\Controller;

class SeoPagesController extends Controller {
    private function renderPage($view, $data = []) {
        extract($data);
        require_once ROOT_PATH . "/pages/seo/_head.php";
        require_once ROOT_PATH . "/pages/seo/{$view}.php";
        require_once ROOT_PATH . "/pages/seo/_footer.php";
        exit;
    }

    public function moveisPlanejados() {
        $this->renderPage('moveis-planejados', [
            'seo_title' => 'Móveis Planejados em Campo Grande | Guia Completo 2026',
            'seo_description' => 'Guia completo sobre móveis planejados em Campo Grande MS. Vantagens, processos, materiais e como escolher a melhor marcenaria para seu projeto.',
            'seo_keywords' => 'móveis planejados, móveis planejados Campo Grande, marcenaria Campo Grande, móveis sob medida MS',
            'seo_heading' => 'Móveis Planejados em Campo Grande',
            'seo_subtitle' => 'Guia completo para transformar sua casa com projetos exclusivos e funcionais',
            'seo_url' => SITE_URL . '/seo/moveis-planejados-campo-grande',
            'page_slug' => 'moveis-planejados-campo-grande',
        ]);
    }

    public function marcenariaCampoGrande() {
        $this->renderPage('marcenaria-campo-grande', [
            'seo_title' => 'Marcenaria em Campo Grande | Profissionais Especializados',
            'seo_description' => 'Encontre a melhor marcenaria em Campo Grande MS. Profissionais especializados em móveis planejados, restauração e projetos personalizados.',
            'seo_keywords' => 'marcenaria Campo Grande, marceneiro Campo Grande, marcenaria MS, móveis planejados',
            'seo_heading' => 'Marcenaria em Campo Grande',
            'seo_subtitle' => 'Tradição e inovação em móveis sob medida para sua casa ou empresa',
            'seo_url' => SITE_URL . '/seo/marcenaria-campo-grande',
            'page_slug' => 'marcenaria-campo-grande',
        ]);
    }

    public function melhorMarcenaria() {
        $this->renderPage('melhor-marcenaria', [
            'seo_title' => 'Melhor Marcenaria de Campo Grande | Qualidade LC Soluções em Móveis',
            'seo_description' => 'Conheça a melhor marcenaria de Campo Grande MS. Qualidade premium, pontualidade e design exclusivo em móveis planejados.',
            'seo_keywords' => 'melhor marcenaria Campo Grande, melhor marceneiro Campo Grande, LC Soluções em Móveis, marcenaria premium',
            'seo_heading' => 'Qual é a Melhor Marcenaria de Campo Grande?',
            'seo_subtitle' => 'Critérios para escolher a marcenaria ideal para seu projeto',
            'seo_url' => SITE_URL . '/seo/melhor-marcenaria-campo-grande',
            'page_slug' => 'melhor-marcenaria-campo-grande',
        ]);
    }

    public function cozinhasPlanejadas() {
        $this->renderPage('cozinhas-planejadas', [
            'seo_title' => 'Cozinhas Planejadas em Campo Grande | Projetos Exclusivos',
            'seo_description' => 'Cozinhas planejadas em Campo Grande. Layouts inteligentes, materiais premium e design personalizado para sua cozinha dos sonhos.',
            'seo_keywords' => 'cozinhas planejadas, cozinha planejada Campo Grande, móveis para cozinha, marcenaria',
            'seo_heading' => 'Cozinhas Planejadas',
            'seo_subtitle' => 'Design funcional e elegante para o coração da sua casa',
            'seo_url' => SITE_URL . '/seo/cozinhas-planejadas',
            'page_slug' => 'cozinhas-planejadas',
        ]);
    }

    public function moveisQuarto() {
        $this->renderPage('moveis-quarto', [
            'seo_title' => 'Móveis para Quarto Planejado | Closet e Dormitórios',
            'seo_description' => 'Móveis planejados para quarto em Campo Grande. Closets, cabeceiras, criados-mudos e projetos que otimizam cada centímetro do seu dormitório.',
            'seo_keywords' => 'móveis para quarto planejado, closet planejado, dormitórios, quarto sob medida',
            'seo_heading' => 'Móveis para Quarto Planejado',
            'seo_subtitle' => 'Transforme seu dormitório em um refúgio de conforto e organização',
            'seo_url' => SITE_URL . '/seo/moveis-quarto-planejado',
            'page_slug' => 'moveis-quarto-planejado',
        ]);
    }

    public function moveisSala() {
        $this->renderPage('moveis-sala', [
            'seo_title' => 'Móveis para Sala de Estar Planejados | Painéis e Estantes',
            'seo_description' => 'Móveis planejados para sala de estar em Campo Grande. Painéis para TV, estantes, home theater e móveis que unem design e funcionalidade.',
            'seo_keywords' => 'móveis para sala de estar planejados, painel para TV, estantes sob medida, sala de estar',
            'seo_heading' => 'Móveis para Sala de Estar Planejados',
            'seo_subtitle' => 'Design e conforto para o ambiente mais social da sua casa',
            'seo_url' => SITE_URL . '/seo/moveis-sala-estar',
            'page_slug' => 'moveis-sala-estar',
        ]);
    }

    public function moveisApartamentos() {
        $this->renderPage('moveis-apartamentos', [
            'seo_title' => 'Móveis Planejados para Apartamentos | Otimização de Espaço',
            'seo_description' => 'Móveis planejados para apartamentos em Campo Grande. Soluções inteligentes que maximizam cada metro quadrado do seu lar.',
            'seo_keywords' => 'móveis planejados para apartamentos, móveis para apartamento pequeno, otimização de espaço',
            'seo_heading' => 'Móveis Planejados para Apartamentos',
            'seo_subtitle' => 'Soluções inteligentes para aproveitar cada metro quadrado',
            'seo_url' => SITE_URL . '/seo/moveis-apartamentos',
            'page_slug' => 'moveis-apartamentos',
        ]);
    }

    public function mdfVsMdp() {
        $this->renderPage('mdf-vs-mdp', [
            'seo_title' => 'MDF vs MDP | Guia Completo para Escolher o Melhor Material',
            'seo_description' => 'Compare MDF e MDP: diferenças, vantagens e desvantagens. Guia completo para escolher o melhor material para seus móveis planejados.',
            'seo_keywords' => 'MDF vs MDP, diferença entre MDF e MDP, qual material escolher, móveis planejados',
            'seo_heading' => 'MDF vs MDP: Qual Material Escolher?',
            'seo_subtitle' => 'Guia completo para entender as diferenças e fazer a melhor escolha',
            'seo_url' => SITE_URL . '/seo/mdf-vs-mdp',
            'page_slug' => 'mdf-vs-mdp',
        ]);
    }

    public function homeOffice() {
        $this->renderPage('home-office', [
            'seo_title' => 'Home Office Planejado | Móveis para Trabalho em Casa',
            'seo_description' => 'Home office planejado em Campo Grande. Móveis ergonômicos e funcionais para trabalhar em casa com conforto e produtividade.',
            'seo_keywords' => 'home office planejado, móveis home office, escritório em casa, mesa sob medida',
            'seo_heading' => 'Home Office Planejado',
            'seo_subtitle' => 'Produtividade e conforto no seu ambiente de trabalho em casa',
            'seo_url' => SITE_URL . '/seo/home-office-planejado',
            'page_slug' => 'home-office-planejado',
        ]);
    }

    public function moveisSobMedida() {
        $this->renderPage('moveis-sob-medida', [
            'seo_title' => 'Móveis Sob Medida em Campo Grande | Projetos Exclusivos',
            'seo_description' => 'Móveis sob medida em Campo Grande. Projetos exclusivos que aproveitam cada espaço da sua casa com design personalizado e qualidade premium.',
            'seo_keywords' => 'móveis sob medida, móveis sob medida Campo Grande, marcenaria sob medida, projetos exclusivos',
            'seo_heading' => 'Móveis Sob Medida',
            'seo_subtitle' => 'Projetos exclusivos criados especialmente para você',
            'seo_url' => SITE_URL . '/seo/moveis-sob-medida',
            'page_slug' => 'moveis-sob-medida',
        ]);
    }
}
