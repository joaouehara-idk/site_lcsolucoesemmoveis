<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;

class SitemapController extends Controller {

    public function blog() {
        header('Content-Type: application/xml; charset=UTF-8');

        $postModel = new Post();
        $posts = $postModel->getAllWithCategory();
        $baseUrl = SITE_URL;

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Main pages
        $mainPages = [
            ['loc' => '/',                          'priority' => '1.0',  'changefreq' => 'weekly'],
            ['loc' => '/sobre',                     'priority' => '0.8',  'changefreq' => 'monthly'],
            ['loc' => '/portfolio',                 'priority' => '0.9',  'changefreq' => 'weekly'],
            ['loc' => '/servicos',                  'priority' => '0.9',  'changefreq' => 'monthly'],
            ['loc' => '/blog',                      'priority' => '0.8',  'changefreq' => 'weekly'],
            ['loc' => '/contato',                   'priority' => '0.8',  'changefreq' => 'monthly'],
            ['loc' => '/clientes',                  'priority' => '0.7',  'changefreq' => 'monthly'],
            ['loc' => '/faq',                       'priority' => '0.7',  'changefreq' => 'monthly'],
            ['loc' => '/politicadeprivacidade',     'priority' => '0.3',  'changefreq' => 'yearly'],
            ['loc' => '/termodeservico',            'priority' => '0.3',  'changefreq' => 'yearly'],
        ];

        foreach ($mainPages as $page) {
            $this->outputUrl($baseUrl . $page['loc'], date('Y-m-d'), $page['changefreq'], $page['priority']);
        }

        // Service pages
        $serviceSlugs = [
            'cozinha-planejada',
            'closet-e-quarto-planejado',
            'sala-e-painel-planejado',
            'home-office-planejado',
            'moveis-comerciais',
        ];

        foreach ($serviceSlugs as $slug) {
            $this->outputUrl($baseUrl . '/servicos/' . $slug, date('Y-m-d'), 'monthly', '0.8');
        }

        // SEO pages
        $seoSlugs = [
            'moveis-planejados-campo-grande',
            'marcenaria-campo-grande',
            'melhor-marcenaria-campo-grande',
            'cozinhas-planejadas',
            'moveis-quarto-planejado',
            'moveis-sala-estar',
            'moveis-apartamentos',
            'mdf-vs-mdp',
            'home-office-planejado',
            'moveis-sob-medida',
        ];

        foreach ($seoSlugs as $slug) {
            $this->outputUrl($baseUrl . '/seo/' . $slug, date('Y-m-d'), 'monthly', '0.6');
        }

        // Blog posts
        foreach ($posts as $post) {
            $lastmod = !empty($post['updated_at'])
                ? date('Y-m-d', strtotime($post['updated_at']))
                : date('Y-m-d', strtotime($post['created_at']));
            $this->outputUrl($baseUrl . '/blog/' . $post['slug'], $lastmod, 'monthly', '0.7');
        }

        echo '</urlset>';
    }

    private function outputUrl($loc, $lastmod, $changefreq, $priority) {
        echo '<url>';
        echo '<loc>' . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . '</loc>';
        echo '<lastmod>' . $lastmod . '</lastmod>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';
    }
}
