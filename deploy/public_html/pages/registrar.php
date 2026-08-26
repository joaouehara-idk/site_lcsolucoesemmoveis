<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F6F1EB;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1A1714;
        }
        .register-container {
            width: 100%;
            max-width: 420px;
            background: #FFFFFF;
            border: 1px solid rgba(26,23,20,0.06);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 4px 16px rgba(26,23,20,0.06);
        }
        .register-container h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 22px;
            font-weight: 400;
            text-align: center;
            margin: 0 0 6px 0;
            color: #1A1714;
        }
        .register-container .subtitle {
            text-align: center;
            color: #8A8580;
            margin-bottom: 28px;
            font-size: 13px;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; margin-bottom: 6px;
            color: #4A4540; font-size: 13px; font-weight: 500;
        }
        .form-group input {
            width: 100%; padding: 13px 16px;
            background: #F6F1EB;
            border: 1.5px solid rgba(26,23,20,0.08);
            color: #1A1714; border-radius: 10px;
            font-size: 14px; font-family: 'Inter', sans-serif;
            transition: all 0.3s ease; box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none; border-color: #1A1714;
            box-shadow: 0 0 0 3px rgba(26,23,20,0.08);
            background: #FFFFFF;
        }
        .form-group input::placeholder { color: #B5B0AA; }
        .btn-register {
            width: 100%; padding: 13px;
            background: #1A1714; border: none;
            color: #F6F1EB; font-weight: 600;
            font-size: 13px; cursor: pointer;
            border-radius: 10px; transition: all 0.3s ease;
            margin-top: 8px; letter-spacing: 0.05em;
            text-transform: uppercase; font-family: 'Inter', sans-serif;
        }
        .btn-register:hover {
            background: #1A1714; color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(26,23,20,0.2);
        }
        .btn-register:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .error-msg {
            background: rgba(184,74,74,0.06);
            border: 1px solid rgba(184,74,74,0.15);
            color: #B84A4A; padding: 12px 16px;
            border-radius: 10px; margin-bottom: 18px;
            font-size: 13px; text-align: center;
        }
        .login-link { text-align: center; margin-top: 20px; color: #8A8580; font-size: 13px; }
        .login-link a { color: #1A1714; text-decoration: none; font-weight: 500; }
        .login-link a:hover { text-decoration: underline; }
        .info-box {
            background: rgba(26,23,20,0.06);
            border: 1px solid rgba(26,23,20,0.12);
            border-radius: 10px; padding: 12px;
            margin-bottom: 24px; font-size: 12px;
            color: #8A8580; text-align: center;
        }
        .honeypot-field {
            position: absolute; left: -9999px; top: -9999px;
            opacity: 0; height: 0; width: 0;
            overflow: hidden; pointer-events: none; tab-index: -1;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Criar Conta</h2>
        <p class="subtitle">Acesso ao painel administrativo</p>

        <div class="info-box">
            Apenas emails autorizados pelo administrador podem se cadastrar.
        </div>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo sanitize($error); ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/registrar" method="POST" id="registerForm" novalidate>
            <?php echo csrf_field(); ?>
            
            <input type="text" name="website_url_confirm" class="honeypot-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="_ts" value="<?php echo time(); ?>">

            <div class="form-group">
                <label for="email">Seu Email</label>
                <input type="email" name="email" id="email" placeholder="seu@email.com" required maxlength="150">
            </div>
            <div class="form-group">
                <label for="usuario">Nome de Usuário</label>
                <input type="text" name="usuario" id="usuario" placeholder="ex: meu nome" required maxlength="150">
            </div>
            <div class="form-group">
                <label for="senha">Sua Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Mínimo 8 caracteres, 1 maiúscula e 1 número" minlength="8" required maxlength="255">
            </div>
            <button type="submit" class="btn-register" id="btnRegister">CRIAR CONTA</button>
        </form>

        <div class="login-link">
            Já tem conta? <a href="<?php echo BASE_URL; ?>/login">Faça login</a>
        </div>
    </div>

    <script>
    (function() {
        var form = document.getElementById('registerForm');
        var btn = document.getElementById('btnRegister');
        var submitted = false;
        form.addEventListener('submit', function() {
            if (submitted) { btn.disabled = true; btn.textContent = 'AGUARDE...'; return false; }
            submitted = true; btn.textContent = 'VERIFICANDO...';
        });
        var tsField = form.querySelector('input[name="_ts"]');
        if (tsField) { tsField.value = Math.floor(Date.now() / 1000); }
    })();
    </script>
</body>
</html>
