<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Login Administrativo'; ?></title>
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
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .login-container {
            background: #FFFFFF;
            border: 1px solid rgba(26,23,20,0.06);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 4px 16px rgba(26,23,20,0.06);
        }
        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }
        .login-header .logo {
            width: 52px;
            height: 52px;
            background: #1A1714;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-family: 'DM Serif Display', serif;
            font-size: 20px;
            color: #F6F1EB;
            letter-spacing: -0.5px;
        }
        .login-header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 22px;
            font-weight: 400;
            color: #1A1714;
            margin: 0 0 6px 0;
        }
        .login-header p {
            color: #8A8580;
            font-size: 13px;
            margin: 0;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #4A4540;
            font-size: 13px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 13px 16px;
            background: #F6F1EB;
            border: 1.5px solid rgba(26,23,20,0.08);
            color: #1A1714;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #1A1714;
            box-shadow: 0 0 0 3px rgba(184,147,90,0.08);
            background: #FFFFFF;
        }
        .form-group input::placeholder { color: #B5B0AA; }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1A1714;
            border: none;
            color: #F6F1EB;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-top: 8px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover {
            background: #1A1714;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(184,147,90,0.2);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .error-msg {
            background: rgba(184,74,74,0.06);
            border: 1px solid rgba(184,74,74,0.15);
            color: #B84A4A;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }
        .success-msg {
            background: rgba(74,124,89,0.06);
            border: 1px solid rgba(74,124,89,0.15);
            color: #4A7C59;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            color: #B5B0AA;
            font-size: 11px;
        }
        .security-badge i { font-size: 11px; }
        @media (max-width: 480px) {
            .login-container { padding: 36px 28px; }
            .login-header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">LC</div>
                <h1>Acesso Administrativo</h1>
                <p>Painel de Gerenciamento</p>
            </div>

            <?php if (isset($error) && $error): ?>
                <div class="error-msg"><?php echo sanitize($error); ?></div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="success-msg"><?php echo sanitize($success); ?></div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/login" method="POST" id="loginForm" novalidate>
                <?php echo csrf_field(); ?>
                
                <div style="position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;width:0;overflow:hidden;" aria-hidden="true">
                    <label for="fax_number">Fax</label>
                    <input type="text" id="fax_number" name="fax_number" autocomplete="off" tabindex="-1">
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="seu@email.com"
                        value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>"
                        required 
                        autofocus
                        autocomplete="email"
                        maxlength="150"
                    >
                </div>

                <div class="form-group" style="margin-bottom:24px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#4A4540;">
                        <input type="checkbox" name="lembrar" value="1" style="width:16px;height:16px;">
                        <span>Lembrar este dispositivo por 30 dias</span>
                    </label>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">ENVIAR CÓDIGO</button>
            </form>

            <div class="security-badge">
                <i class="fas fa-lock"></i>
                <span>Conexão segura</span>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var form = document.getElementById('loginForm');
        var btn = document.getElementById('btnLogin');
        var submitted = false;
        form.addEventListener('submit', function(e) {
            if (submitted) { e.preventDefault(); return false; }
            submitted = true;
            btn.disabled = true;
            btn.textContent = 'ENTRANDO...';
        });
    })();
    </script>
</body>
</html>
