<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Projeto extends Model {
    protected $table = 'projetos';

    public function all() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    public function findWithImages($id) {
        $projeto = $this->find($id);
        if ($projeto) {
            $projeto['imagens'] = $this->db->fetchAll("SELECT * FROM projeto_imagens WHERE projeto_id = ? ORDER BY ordem ASC", [$id]);
        }
        return $projeto;
    }

    public function getByCategory($category) {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE categoria = :categoria ORDER BY created_at DESC", ['categoria' => $category]);
    }

    public function incrementViews($id) {
        return $this->db->query("UPDATE {$this->table} SET visualizacoes = visualizacoes + 1 WHERE id = ?", [$id]);
    }

    public function getTopViewed($limit = 5) {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY visualizacoes DESC LIMIT " . (int)$limit);
    }

    public function addImage($projetoId, $path) {
        return $this->db->query("INSERT INTO projeto_imagens (projeto_id, caminho) VALUES (?, ?)", [$projetoId, $path]);
    }

    public function getImages($projetoId) {
        return $this->db->fetchAll("SELECT * FROM projeto_imagens WHERE projeto_id = ? ORDER BY ordem ASC", [$projetoId]);
    }

    public function search($term) {
        $term = "%$term%";
        return $this->db->fetchAll("
            SELECT * FROM {$this->table} 
            WHERE (titulo LIKE ? OR descricao LIKE ? OR categoria LIKE ?)
            ORDER BY created_at DESC
        ", [$term, $term, $term]);
    }
}
