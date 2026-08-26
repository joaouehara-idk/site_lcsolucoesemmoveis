<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return !empty($_SESSION['usuario_id']);
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['usuario_role'] ?? '') === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Nao autenticado']);
            exit;
        }
        header('Location: login.php');
        exit;
    }
}

function getUserInfo() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome'],
        'email' => $_SESSION['usuario_email'],
        'role' => $_SESSION['usuario_role'],
    ];
}
