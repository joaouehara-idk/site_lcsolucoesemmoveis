<?php

namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller {
    public function privacy() {
        return $this->render('politicadeprivacidade', [
            'title' => 'Política de Privacidade | LC Soluções em Móveis',
            'description' => 'Nossa política de privacidade e compromisso com seus dados.'
        ]);
    }

    public function terms() {
        return $this->render('termodeservico', [
            'title' => 'Termos de Serviço | LC Soluções em Móveis',
            'description' => 'Nossos termos de serviço e condições de uso.'
        ]);
    }
}
