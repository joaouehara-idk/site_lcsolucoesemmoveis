<?php

namespace App\Models;

use App\Core\Model;

class Contato extends Model {
    protected $table = 'contatos';

    public function allOrdered() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY data_envio DESC");
    }

    public function save($data) {
        // 1. Salva no banco do site (meusite_db.contatos)
        $sql = "INSERT INTO {$this->table} (nome, email, telefone, assunto, mensagem, ip, status) 
                VALUES (:nome, :email, :telefone, :assunto, :mensagem, :ip, 'novo')";
        
        $this->db->query($sql, [
            'nome'     => $data['nome'],
            'email'    => !empty($data['email']) ? $data['email'] : '',
            'telefone' => $data['telefone'] ?? null,
            'assunto'  => $data['assunto'] ?? 'Contato via Site',
            'mensagem' => $data['mensagem'],
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // 2. Sincroniza para o CRM (crm_cnpj.clientes)
        $this->syncToCrm($data);
    }

    private function syncToCrm($data) {
        try {
            $crm = new \PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=crm_cnpj;charset=utf8mb4',
                'root',
                '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            // Evita duplicatas: busca se ja existe cliente com mesmo email ou telefone
            $stmt = $crm->prepare("SELECT id FROM clientes WHERE email = ? OR telefone = ? LIMIT 1");
            $stmt->execute([$data['email'] ?? null, $data['telefone'] ?? null]);
            $existing = $stmt->fetch();

            $obs = "Assunto: {$data['assunto']}\nMensagem: {$data['mensagem']}";

            if ($existing) {
                // Atualiza contato existente
                $upd = $crm->prepare("UPDATE clientes SET origem = 'site', ultimo_contato = CURDATE(), observacoes = CONCAT(observacoes, '\n---\n', ?) WHERE id = ?");
                $upd->execute([$obs, $existing['id']]);
            } else {
                // Insere novo lead no CRM
                $ins = $crm->prepare("INSERT INTO clientes (razao_social, nome_fantasia, telefone, email, origem, status, contato_inicial, ultimo_contato, observacoes) VALUES (?, ?, ?, ?, 'site', 'lead', CURDATE(), CURDATE(), ?)");
                $ins->execute([
                    $data['nome'],
                    $data['nome'],
                    $data['telefone'] ?? null,
                    $data['email'],
                    $obs
                ]);
            }
        } catch (\Exception $e) {
            // Loga erro mas nao quebra o fluxo do formulario
            error_log("CRM sync error: " . $e->getMessage());
        }
    }
}
