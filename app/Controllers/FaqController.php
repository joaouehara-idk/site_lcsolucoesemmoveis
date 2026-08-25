<?php

namespace App\Controllers;

use App\Core\Controller;

class FaqController extends Controller {
    public function index() {
        return $this->render('faq', [
            'title' => 'Dúvidas Frequentes | FAQ LC Soluções em Móveis',
            'description' => 'Tire suas dúvidas sobre o processo de móveis planejados.'
        ]);
    }
}
