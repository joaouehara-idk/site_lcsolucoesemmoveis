<?php

namespace App\Controllers;

use App\Core\Controller;

class ClientesController extends Controller {
    public function index() {
        return $this->render('clientes', [
            'title' => 'Nossos Clientes | Depoimentos e Cases',
            'description' => 'Veja o que dizem os clientes da LC Soluções em Móveis.'
        ]);
    }
}
