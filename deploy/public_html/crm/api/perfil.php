<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$userId = (int)$_SESSION['usuario_id'];
$action = $_GET['action'] ?? '';

if ($action === 'info') {
    $stmt = $conn->prepare("SELECT id, nome, email, role, created_at FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Usuario nao encontrado']);
        exit;
    }
    echo json_encode(['success' => true, 'user' => $user]);
    exit;
}

if ($action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Dados invalidos']);
        exit;
    }

    $nome = trim($input['nome'] ?? '');
    $email = trim($input['email'] ?? '');

    if (!$nome || !$email) {
        echo json_encode(['success' => false, 'error' => 'Nome e email sao obrigatorios']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Email invalido']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $check->bind_param('si', $email, $userId);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Este email ja esta em uso']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
    $stmt->bind_param('ssi', $nome, $email, $userId);
    $stmt->execute();

    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_email'] = $email;

    echo json_encode(['success' => true, 'message' => 'Perfil atualizado']);
    exit;
}

if ($action === 'password') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Dados invalidos']);
        exit;
    }

    $current = $input['current_password'] ?? '';
    $new = $input['new_password'] ?? '';
    $confirm = $input['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) {
        echo json_encode(['success' => false, 'error' => 'Preencha todos os campos de senha']);
        exit;
    }

    if ($new !== $confirm) {
        echo json_encode(['success' => false, 'error' => 'Nova senha e confirmacao nao conferem']);
        exit;
    }

    if (strlen($new) < 4) {
        echo json_encode(['success' => false, 'error' => 'Nova senha deve ter no minimo 4 caracteres']);
        exit;
    }

    $stmt = $conn->prepare("SELECT senha_hash FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $row['senha_hash'])) {
        echo json_encode(['success' => false, 'error' => 'Senha atual incorreta']);
        exit;
    }

    $hash = password_hash($new, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
    $stmt->bind_param('si', $hash, $userId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acao invalida']);

