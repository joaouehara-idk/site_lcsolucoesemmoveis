<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\User;
use App\Services\LoginSecurityService;
use App\Services\TokenService;
use App\Services\TrustedDeviceService;

class AuthController extends Controller {
    private $security;
    private $tokenService;
    private $deviceService;

    public function __construct() {
        $this->security = new LoginSecurityService();
        $this->tokenService = new TokenService();
        $this->deviceService = new TrustedDeviceService();
    }

    // ─── ETAPA 1: Tela de Email → Envio de Token ────────────────

    public function showLogin() {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();

            // Trusted device check — bulletproof, never crashes
            try {
                $trustedToken = $this->deviceService->getToken();
                if ($trustedToken) {
                    $userId = $this->deviceService->validateTrustedDevice($trustedToken);
                    if ($userId) {
                        $userModel = new User();
                        $user = $userModel->find($userId);

                        if ($user) {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['usuario'];
                            $_SESSION['user_email'] = $user['email'] ?? '';
                            $_SESSION['logged_in_at'] = time();
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
                            $_SESSION['last_activity'] = time();

                            try {
                                $ip = $this->security->getClientIp();
                                $this->security->recordLogin($user['id'], $ip);
                            } catch (\Throwable $e) {
                                // Non-critical: login recording failed
                            }

                            return $this->redirect('/admin/dashboard');
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Trusted device validation failed — fall through to normal login
                try { Logger::warning('Trusted device check failed: ' . $e->getMessage()); } catch (\Throwable $x) {}
            }

            $error = $_SESSION['flash_error'] ?? null;
            $success = $_SESSION['flash_success'] ?? null;
            unset($_SESSION['flash_error'], $_SESSION['flash_success']);

            return $this->renderRaw('admin.login', [
                'title' => 'Login Administrativo',
                'error' => $error,
                'success' => $success
            ]);
        } catch (\Throwable $e) {
            // Last resort — show login form no matter what
            try { Logger::error('showLogin fatal: ' . $e->getMessage()); } catch (\Throwable $x) {}
            @http_response_code(200);
            @header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html><head><title>Login</title>';
            echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#F6F1EB;color:#1A1714;}';
            echo '.box{text-align:center;padding:40px;background:#fff;border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,0.06);max-width:400px;width:90%;}';
            echo '.box h1{font-size:20px;margin-bottom:8px;}.box p{color:#8A8580;font-size:13px;margin-bottom:20px;}';
            echo 'a{color:#1A1714;text-decoration:underline;font-size:14px;}</style></head><body>';
            echo '<div class="box"><h1>Login Administrativo</h1>';
            echo '<p>Ocorreu um erro temporário. Por favor, tente novamente.</p>';
            echo '<a href="' . (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'lcsolucoesemmoveis.com.br') . '/login">Tentar novamente</a>';
            echo '</div></body></html>';
            exit;
        }
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = 'Token de segurança inválido. Tente novamente.';
            return $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $_SESSION['flash_error'] = 'Informe seu e-mail.';
            return $this->redirect('/login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $_SESSION['flash_error'] = 'E-mail inválido.';
            return $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['flash_error'] = 'E-mail não encontrado no sistema.';
            return $this->redirect('/login');
        }

        $token = $this->tokenService->generateToken($user['id']);

        $_SESSION['pending_2fa_user_id'] = $user['id'];
        $_SESSION['pending_2fa_email'] = $user['email'];
        $_SESSION['pending_2fa_username'] = $user['usuario'];

        if (isset($_POST['lembrar']) && $_POST['lembrar'] == '1') {
            $_SESSION['lembrar_device'] = true;
        }

        $emailSent = $this->tokenService->sendTokenEmail(
            $user['email'],
            $token,
            $user['usuario']
        );

        if (!$emailSent) {
            try { Logger::error("Falha ao enviar token para: {$user['email']}"); } catch (\Throwable $e) {}
            $_SESSION['flash_error'] = 'Erro ao enviar o código por e-mail. Tente novamente ou entre em contato com o suporte.';
            return $this->redirect('/login');
        }

        try { Logger::info("Login 2FA iniciado para: {$user['email']}, redirecionando para /verificar-token"); } catch (\Throwable $e) {}
        return $this->redirect('/verificar-token');
    }

    // ─── ETAPA 2: Tela de Verificação do Token ──────────────────

    public function showVerifyToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        try { Logger::info('showVerifyToken chamado. pending_2fa_user_id=' . ($_SESSION['pending_2fa_user_id'] ?? 'null')); } catch (\Throwable $e) {}

        if (empty($_SESSION['pending_2fa_user_id'])) {
            try { Logger::warning('showVerifyToken: sessão pending_2fa_user_id vazia, redirecionando para login'); } catch (\Throwable $e) {}
            $_SESSION['flash_error'] = 'Sessão expirada. Faça login novamente.';
            return $this->redirect('/login');
        }

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return $this->renderRaw('admin.verify_token', [
            'title' => 'Verificar Código',
            'email' => $_SESSION['pending_2fa_email'] ?? '',
            'error' => $error,
            'success' => $success
        ]);
    }

    public function verifyToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = 'Token de segurança inválido. Tente novamente.';
            return $this->redirect('/verificar-token');
        }

        if (empty($_SESSION['pending_2fa_user_id'])) {
            $_SESSION['flash_error'] = 'Sessão expirada. Faça login novamente.';
            return $this->redirect('/login');
        }

        $userId = $_SESSION['pending_2fa_user_id'];
        $token = trim($_POST['token'] ?? '');

        if (empty($token) || strlen($token) !== 6) {
            $_SESSION['flash_error'] = 'Digite o código de 6 dígitos.';
            return $this->redirect('/verificar-token');
        }

        if ($this->tokenService->validateToken($userId, $token)) {
            $userModel = new User();
            $user = $userModel->find($userId);

            if (!$user) {
                $_SESSION['flash_error'] = 'Usuário não encontrado.';
                return $this->redirect('/login');
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['usuario'];
            $_SESSION['user_email'] = $user['email'] ?? '';
            $_SESSION['logged_in_at'] = time();
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['last_activity'] = time();

            try {
                $ip = $this->security->getClientIp();
                $this->security->recordLogin($user['id'], $ip);
            } catch (\Throwable $e) {
                // Non-critical
            }

            if (!empty($_SESSION['lembrar_device'])) {
                try {
                    $deviceToken = $this->deviceService->createTrustedDevice($user['id']);
                    $this->deviceService->setCookie($deviceToken);
                    try { Logger::info("Trusted device cookie set for user_id: {$user['id']}"); } catch (\Throwable $e) {}
                } catch (\Throwable $e) {
                    try { Logger::error("Failed to create trusted device: " . $e->getMessage()); } catch (\Throwable $x) {}
                }
                unset($_SESSION['lembrar_device']);
            }

            unset(
                $_SESSION['pending_2fa_user_id'],
                $_SESSION['pending_2fa_email'],
                $_SESSION['pending_2fa_username']
            );

            return $this->redirect('/admin/dashboard');
        }

        $_SESSION['flash_error'] = 'Código inválido ou expirado. Solicite um novo código.';
        return $this->redirect('/verificar-token');
    }

    public function resendToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = 'Token de segurança inválido. Tente novamente.';
            return $this->redirect('/verificar-token');
        }

        if (empty($_SESSION['pending_2fa_user_id'])) {
            $_SESSION['flash_error'] = 'Sessão expirada. Faça login novamente.';
            return $this->redirect('/login');
        }

        $userId = $_SESSION['pending_2fa_user_id'];
        $email = $_SESSION['pending_2fa_email'];
        $username = $_SESSION['pending_2fa_username'];

        $token = $this->tokenService->generateToken($userId);

        $sent = $this->tokenService->sendTokenEmail($email, $token, $username);
        if (!$sent) {
            $_SESSION['flash_error'] = 'Erro ao reenviar o código. Tente novamente em instantes.';
            return $this->redirect('/verificar-token');
        }

        $_SESSION['flash_success'] = 'Novo código enviado para ' . $email;
        return $this->redirect('/verificar-token');
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        try {
            $trustedToken = $this->deviceService->getToken();
            if ($trustedToken) {
                $this->deviceService->clearTrustedDevice($trustedToken);
                $this->deviceService->clearCookie();
            }
        } catch (\Throwable $e) {
            // Non-critical
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        session_destroy();

        return $this->redirect('/login');
    }
}
