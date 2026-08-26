<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if ($email && $senha) {
        require_once __DIR__ . '/includes/db.php';
        $stmt = $conn->prepare("SELECT id, nome, email, senha_hash, role FROM usuarios WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if ($r && password_verify($senha, $r['senha_hash'])) {
            $_SESSION['usuario_id'] = $r['id'];
            $_SESSION['usuario_nome'] = $r['nome'];
            $_SESSION['usuario_email'] = $r['email'];
            $_SESSION['usuario_role'] = $r['role'];
            header('Location: index.php');
            exit;
        }
        $erro = 'Email ou senha inválidos.';
    } else {
        $erro = 'Preencha email e senha.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LC CRM - Login</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
:root {
    --gold: #c5a253;
    --gold-light: #d4b66a;
    --gold-dark: #a8883a;
    --bg: #0a0a0a;
    --bg-card: #111;
    --text: #e0ddd5;
    --text-muted: #888;
    --border: rgba(255,255,255,0.06);
    --radius-lg: 16px;
    --radius-md: 10px;
    --font-body: 'Inter', system-ui, -apple-system, sans-serif;
    --font-heading: 'Inter', system-ui, sans-serif;
}
body {
    font-family: var(--font-body);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 40px;
    width: 100%;
    max-width: 380px;
    text-align: center;
}
.login-box h1 {
    font-family: var(--font-heading);
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 4px;
    background: linear-gradient(135deg, var(--gold-light), var(--gold), var(--gold-dark));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
.login-box p {
    color: var(--text-muted);
    font-size: 13px;
    margin-bottom: 28px;
}
.login-box .erro {
    background: rgba(231,76,60,0.1);
    color: #e74c3c;
    padding: 10px 14px;
    border-radius: var(--radius-md);
    font-size: 12px;
    margin-bottom: 16px;
    border: 1px solid rgba(231,76,60,0.2);
}
.login-box label {
    display: block;
    text-align: left;
    font-size: 11px;
    color: var(--gold);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.login-box input {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(0,0,0,.5);
    color: var(--text);
    font-size: 14px;
    font-family: var(--font-body);
    outline: none;
    margin-bottom: 18px;
    transition: border-color .2s;
}
.login-box input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(197,162,83,0.15);
}
.login-box button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #000;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 700;
    font-family: var(--font-body);
    cursor: pointer;
    transition: all .25s;
}
.login-box button:hover {
    background: linear-gradient(135deg, var(--gold-light), var(--gold));
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(197,162,83,0.3);
}
</style>
</head>
<body>
<div class="login-box">
    <h1>LC CRM</h1>
    <p>Faça login para acessar o sistema</p>
    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" placeholder="seu@email.com" required autofocus>
        <label>Senha</label>
        <input type="password" name="senha" placeholder="••••••" required>
        <button type="submit">Entrar</button>
    </form>
</div>
</body>
</html>
