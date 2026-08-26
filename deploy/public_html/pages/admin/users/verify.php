<h1>Autorização de Novo Usuário</h1>
<p>Um código de autorização foi enviado para o e-mail do <strong>Administrador Principal</strong>.</p>

<div class="card" style="max-width: 400px;">
    <?php if (isset($error)): ?>
        <p style="color: #ff6b6b; margin-bottom: 15px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/admin/usuarios/confirmar" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="codigo">Código de Autorização</label>
            <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Digite o código enviado ao dono" required maxlength="6">
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn-save" style="width: 100%;">Autorizar e Criar Conta</button>
        </div>
    </form>
    
    <p style="margin-top: 15px; font-size: 0.85rem; color: #777;">
        <?php if ($_SERVER['HTTP_HOST'] == 'localhost'): ?>
            <strong style="color: var(--accent);">DEBUG (Localhost): O código que chegou no e-mail do admin é: <?php echo $_SESSION['pending_user']['codigo']; ?></strong>
        <?php else: ?>
            Solicite o código ao proprietário do sistema para concluir o cadastro.
        <?php endif; ?>
    </p>
</div>
