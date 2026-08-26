<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contato;

class ContatoController extends Controller {
    public function index() {
        return $this->render('contato', [
            'title' => 'Fale Conosco | LC Soluções em Móveis',
            'description' => 'Entre em contato e solicite seu orçamento de móveis planejados em Campo Grande.'
        ]);
    }

    public function enviar() {
        // Lógica de processamento do formulário (antigo processa_contato.php)
        // IMPORTANTE: ler de $_POST diretamente. filter_input(INPUT_POST, ...)
        // retorna vazio para requisições multipart/form-data (fetch + FormData),
        // fazendo a validação falhar com "Campos inválidos."
        $nome     = trim((string)($_POST['nome'] ?? ''));
        $email    = trim((string)($_POST['email'] ?? ''));
        $telefone = trim((string)($_POST['whatsapp'] ?? ''));
        $assunto  = trim((string)($_POST['assunto'] ?? ''));
        $mensagem = trim((string)($_POST['mensagem'] ?? ''));

        if ($nome === '' || $telefone === '' || $assunto === '' || $mensagem === '') {
            return $this->json(['success' => false, 'message' => 'Campos inválidos.']);
        }

        $data = [
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'assunto'  => $assunto,
            'mensagem' => $mensagem
        ];

        try {
            $model = new Contato();
            $model->save($data);
            return $this->json(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Erro ao salvar contato.']);
        }
    }
}
