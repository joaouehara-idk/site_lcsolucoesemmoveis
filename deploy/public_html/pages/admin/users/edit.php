<h1>Editar Usuário</h1>

<div class="card">
    <form action="<?php echo BASE_URL; ?>/admin/usuarios/atualizar" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        
        <div class="form-group">
            <label for="usuario">Nome de Usuário</label>
            <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo htmlspecialchars($user['usuario']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha (deixe em branco para não alterar)</label>
            <input type="password" name="senha" id="senha" class="form-control">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save">Salvar Alterações</button>
            <a href="<?php echo BASE_URL; ?>/admin/usuarios" style="color: #ccc; margin-left: 20px;">Cancelar</a>
        </div>
    </form>
</div>
