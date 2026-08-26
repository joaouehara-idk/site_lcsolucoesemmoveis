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
        if (session_status() === PHP_SESSION_NONE) session_start();

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

                    $ip = $this->security->getClientIp();
                    $this->security->recordLogin($user['id'], $ip);

                    return $this->redirect('/admin/dashboard');
                }
            }
        }

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return $this->renderRaw('admin.login', [
            'title' => 'Login Administrativo',
            'error' => $error,
            'success' => $success
        ]);
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
            $_SESSION['flash_error'] = 'Erro ao enviar o código por e-mail. Tente novamente ou entre em contato com o suporte.';
            return $this->redirect('/login');
        }

        return $this->redirect('/verificar-token');
    }

    // ─── ETAPA 2: Tela de Verificação do Token ──────────────────

    public function showVerifyToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['pending_2fa_user_id'])) {
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

            $ip = $this->security->getClientIp();
            $this->security->recordLogin($user['id'], $ip);

            if (!empty($_SESSION['lembrar_device'])) {
                $deviceToken = $this->deviceService->createTrustedDevice($user['id']);
                $this->deviceService->setCookie($deviceToken);
                Logger::info("Trusted device cookie set for user_id: {$user['id']}");
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

        $trustedToken = $this->deviceService->getToken();
        if ($trustedToken) {
            $this->deviceService->clearTrustedDevice($trustedToken);
            $this->deviceService->clearCookie();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        session_destroy();

        return $this->redirect('/login');
    }

    // Registro removido - apenas o admin supremo cria usuários via /admin/usuarios
}
