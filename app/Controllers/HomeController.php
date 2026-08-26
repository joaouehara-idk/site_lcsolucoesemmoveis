<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        return $this->render('home', [
            'title' => 'LC Soluções em Móveis | Móveis Planejados em Campo Grande',
            'description' => 'Transforme sua casa com móveis planejados de alta qualidade. Orçamento gratuito em Campo Grande/MS.'
        ]);
    }
}
