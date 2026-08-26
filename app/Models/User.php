<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'usuarios';

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function findByUsername($username) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE usuario = ?", [$username]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (usuario, senha, email) VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function createWithHashedPassword($data) {
        $sql = "INSERT INTO {$this->table} (usuario, senha, email) VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $data['username'],
            $data['password'],
            $data['email'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function recordLogin($userId, $ip) {
        $this->db->query(
            "UPDATE {$this->table} SET ultimo_login = NOW(), ultimo_ip = ? WHERE id = ?",
            [$ip, $userId]
        );
    }

    public function incrementAttempts($userId) {
        $this->db->query(
            "UPDATE {$this->table} SET tentativas_login = tentativas_login + 1 WHERE id = ?",
            [$userId]
        );
    }

    public function resetAttempts($userId) {
        $this->db->query(
            "UPDATE {$this->table} SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?",
            [$userId]
        );
    }

    public function lockAccount($userId, $minutes = 15) {
        $unlockAt = date('Y-m-d H:i:s', time() + ($minutes * 60));
        $this->db->query(
            "UPDATE {$this->table} SET bloqueado_ate = ? WHERE id = ?",
            [$unlockAt, $userId]
        );
    }

    public function isLocked($userId) {
        $user = $this->find($userId);
        if (!$user || empty($user['bloqueado_ate'])) return false;
        
        if (strtotime($user['bloqueado_ate']) > time()) {
            return true;
        }
        
        $this->resetAttempts($userId);
        return false;
    }
}
