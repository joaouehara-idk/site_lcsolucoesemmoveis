<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Verificar Código'; ?></title>
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
        .login-wrapper { width: 100%; max-width: 420px; }
        .login-container {
            background: #FFFFFF;
            border: 1px solid rgba(26,23,20,0.06);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 4px 16px rgba(26,23,20,0.06);
        }
        .login-header { text-align: center; margin-bottom: 28px; }
        .login-header .logo {
            width: 52px; height: 52px;
            background: #1A1714;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-family: 'DM Serif Display', serif;
            font-size: 20px; color: #F6F1EB;
        }
        .login-header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 22px; font-weight: 400;
            color: #1A1714; margin: 0 0 6px 0;
        }
        .login-header p { color: #8A8580; font-size: 13px; margin: 0; }
        .step-indicator {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; margin-bottom: 28px;
        }
        .step {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #B5B0AA;
        }
        .step.active { color: #1A1714; }
        .step .dot {
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--bg); border: 1.5px solid rgba(26,23,20,0.08);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 11px;
        }
        .step.active .dot {
            background: rgba(184,147,90,0.08);
            border-color: #1A1714; color: #1A1714;
        }
        .step.done .dot {
            background: #1A1714; border-color: #1A1714; color: #F6F1EB;
        }
        .step-line { width: 28px; height: 1.5px; background: rgba(26,23,20,0.08); }
        .token-icon {
            text-align: center; margin-bottom: 20px;
        }
        .token-icon i { font-size: 2.2rem; color: #1A1714; opacity: 0.7; }
        .token-info {
            text-align: center; margin-bottom: 24px;
            color: #8A8580; font-size: 13px; line-height: 1.6;
        }
        .token-info strong { color: #1A1714; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; margin-bottom: 6px;
            color: #4A4540; font-size: 13px; font-weight: 500;
        }
        .form-group input {
            width: 100%; padding: 16px;
            background: #F6F1EB;
            border: 1.5px solid rgba(26,23,20,0.08);
            color: #1A1714; border-radius: 10px;
            font-size: 28px; font-weight: 700;
            font-family: 'Courier New', monospace;
            text-align: center; letter-spacing: 10px;
            transition: all 0.3s ease; box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none; border-color: #1A1714;
            box-shadow: 0 0 0 3px rgba(184,147,90,0.08);
            background: #FFFFFF;
        }
        .form-group input::placeholder {
            color: #D5D0CA; font-size: 16px;
            letter-spacing: 2px; font-weight: 400;
        }
        .btn-verify {
            width: 100%; padding: 13px;
            background: #1A1714; border: none;
            color: #F6F1EB; font-weight: 600;
            font-size: 13px; cursor: pointer;
            border-radius: 10px; transition: all 0.3s ease;
            margin-top: 8px; letter-spacing: 0.05em;
            text-transform: uppercase; font-family: 'Inter', sans-serif;
        }
        .btn-verify:hover {
            background: #1A1714; color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(184,147,90,0.2);
        }
        .btn-verify:active { transform: translateY(0); }
        .btn-verify:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .error-msg {
            background: rgba(184,74,74,0.06);
            border: 1px solid rgba(184,74,74,0.15);
            color: #B84A4A; padding: 12px 16px;
            border-radius: 10px; margin-bottom: 18px;
            font-size: 13px; text-align: center;
        }
        .success-msg {
            background: rgba(74,124,89,0.06);
            border: 1px solid rgba(74,124,89,0.15);
            color: #4A7C59; padding: 12px 16px;
            border-radius: 10px; margin-bottom: 18px;
            font-size: 13px; text-align: center;
        }
        .resend-area {
            text-align: center; margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(26,23,20,0.06);
        }
        .resend-area p { color: #B5B0AA; font-size: 13px; margin-bottom: 8px; }
        .resend-btn {
            background: none; border: none;
            color: #1A1714; font-size: 13px;
            cursor: pointer; text-decoration: underline;
            font-family: 'Inter', sans-serif; padding: 0;
        }
        .resend-btn:hover { color: #1A1714; }
        .resend-btn:disabled { color: #B5B0AA; cursor: not-allowed; text-decoration: none; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #B5B0AA; font-size: 12px; text-decoration: none; }
        .back-link a:hover { color: #8A8580; }
        .security-badge {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 20px; color: #B5B0AA; font-size: 11px;
        }
        .security-badge i { font-size: 11px; }
        @media (max-width: 480px) {
            .login-container { padding: 36px 28px; }
            .login-header h1 { font-size: 20px; }
            .form-group input { font-size: 24px; letter-spacing: 8px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">LC</div>
                <h1>Verificação em 2 Etapas</h1>
                <p>Confirme sua identidade</p>
            </div>

            <div class="step-indicator">
                <div class="step done">
                    <span class="dot">&#10003;</span>
                    <span>E-mail</span>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <span class="dot">2</span>
                    <span>Código</span>
                </div>
            </div>

            <div class="token-icon">
                <i class="fas fa-key"></i>
            </div>

            <div class="token-info">
                Enviamos um código de 6 dígitos para<br>
                <strong><?php echo sanitize($email ?? ''); ?></strong>
            </div>

            <?php if (isset($error) && $error): ?>
                <div class="error-msg"><?php echo sanitize($error); ?></div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="success-msg"><?php echo sanitize($success); ?></div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/verificar-token" method="POST" id="tokenForm" novalidate>
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="token">Código de Verificação</label>
                    <input 
                        type="text" name="token" id="token"
                        placeholder="000000" maxlength="6"
                        pattern="[0-9]{6}" inputmode="numeric"
                        autocomplete="one-time-code"
                        required autofocus
                    >
                </div>

                <button type="submit" class="btn-verify" id="btnVerify">VERIFICAR E ACESSAR</button>
            </form>

            <div class="resend-area">
                <p>Não recebeu o código?</p>
                <form action="<?php echo BASE_URL; ?>/reenviar-token" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="resend-btn" id="btnResend">Reenviar código</button>
                </form>
            </div>

            <div class="back-link">
                <a href="<?php echo BASE_URL; ?>/login">&larr; Voltar ao login</a>
            </div>

            <div class="security-badge">
                <i class="fas fa-lock"></i>
                <span>Código expira em 10 minutos</span>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var form = document.getElementById('tokenForm');
        var btn = document.getElementById('btnVerify');
        var submitted = false;
        form.addEventListener('submit', function() {
            if (submitted) { btn.disabled = true; btn.textContent = 'VERIFICANDO...'; return false; }
            submitted = true; btn.textContent = 'VERIFICANDO...';
        });
        var tokenInput = document.getElementById('token');
        tokenInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    })();
    </script>
</body>
</html>
