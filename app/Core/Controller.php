<?php

namespace App\Core;

abstract class Controller {
    protected function render($view, $data = []) {
        extract($data);
        $viewPath = ROOT_PATH . "/pages/" . str_replace('.', '/', $view) . ".php";
        
        if (file_exists($viewPath)) {
            // Se for admin, inclui header e footer do admin
            if (strpos($view, 'admin.') === 0) {
                require_once ROOT_PATH . "/includes/admin_header.php";
            } else {
                require_once ROOT_PATH . "/includes/head.php";
                require_once ROOT_PATH . "/includes/header.php";
            }

            require_once $viewPath;

            if (strpos($view, 'admin.') === 0) {
                require_once ROOT_PATH . "/includes/admin_footer.php";
            } else {
                require_once ROOT_PATH . "/includes/footer.php";
            }
        } else {
            throw new \Exception("View $view not found at $viewPath");
        }
    }

    protected function renderRaw($view, $data = []) {
        extract($data);
        $viewPath = ROOT_PATH . "/pages/" . str_replace('.', '/', $view) . ".php";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("View $view not found at $viewPath");
        }
        exit;
    }

    protected function json($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        // Se a URL for relativa (não começar com http/https), adiciona o BASE_URL
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header("Location: " . $url);
        exit;
    }
}
