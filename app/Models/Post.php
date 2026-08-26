<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Post extends Model {
    protected $table = 'posts';

    public function getAllWithCategory() {
        return $this->db->fetchAll("
            SELECT p.*, c.nome as categoria_nome 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.status = 'publicado' 
            ORDER BY p.created_at DESC
        ");
    }

    public function getBySlug($slug) {
        return $this->db->fetch("
            SELECT p.*, c.nome as categoria_nome 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.slug = ? LIMIT 1
        ", [$slug]);
    }

    public function incrementViews($id) {
        return $this->db->query("UPDATE {$this->table} SET visualizacoes = visualizacoes + 1 WHERE id = ?", [$id]);
    }

    public function getCategoriesWithCount() {
        return $this->db->fetchAll("
            SELECT COALESCE(c.nome, p.categoria) as categoria, COUNT(*) as total 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.status = 'publicado' 
            AND (p.categoria IS NOT NULL OR p.categoria_id IS NOT NULL) 
            GROUP BY categoria 
            ORDER BY total DESC
        ");
    }

    public function getTopViewed($limit = 5) {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY visualizacoes DESC LIMIT " . (int)$limit);
    }

    public function getByCategoria($categoria) {
        $cat = "%$categoria%";
        return $this->db->fetchAll("
            SELECT p.*, c.nome as categoria_nome 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE (p.categoria LIKE ? OR c.nome LIKE ?)
            AND p.status = 'publicado' 
            ORDER BY p.created_at DESC
        ", [$cat, $cat]);
    }

    public function getRelated($id, $categoria, $limit = 3) {
        if (empty($categoria)) {
            return $this->db->fetchAll("
                SELECT p.*, c.nome as categoria_nome 
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id != ? AND p.status = 'publicado' 
                ORDER BY p.visualizacoes DESC 
                LIMIT ?
            ", [$id, (int)$limit]);
        }
        return $this->db->fetchAll("
            SELECT p.*, c.nome as categoria_nome 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.id != ? AND p.status = 'publicado' 
            AND (p.categoria LIKE ? OR c.nome LIKE ?)
            ORDER BY p.visualizacoes DESC 
            LIMIT ?
        ", [$id, "%$categoria%", "%$categoria%", (int)$limit]);
    }

    public function search($term) {
        $term = "%$term%";
        return $this->db->fetchAll("
            SELECT p.*, c.nome as categoria_nome 
            FROM {$this->table} p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE (p.titulo LIKE ? OR p.conteudo LIKE ? OR COALESCE(c.nome, p.categoria) LIKE ?)
            AND p.status = 'publicado' 
            ORDER BY p.created_at DESC
        ", [$term, $term, $term]);
    }
}
