<?php

namespace App\Controllers;

use App\Core\Controller;

class SobreController extends Controller {
    public function index() {
        return $this->render('sobre', [
            'title' => 'Sobre Nós | LC Soluções em Móveis',
            'description' => 'Conheça a história e o compromisso com a qualidade da LC Soluções em Móveis.'
        ]);
    }
}
