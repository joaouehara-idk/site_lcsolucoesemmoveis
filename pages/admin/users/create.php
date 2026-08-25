<h1>Novo Usuário</h1>
<div class="card" style="max-width: 500px;">
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="<?php echo BASE_URL; ?>/admin/usuarios/salvar" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="usuario">Nome de Usuário</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn-save">ENVIAR CÓDIGO DE VERIFICAÇÃO</button>
    </form>
</div>
