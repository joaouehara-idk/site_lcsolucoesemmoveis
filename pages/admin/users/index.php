<h1>Gerenciar Usuários</h1>
<?php if ($is_master): ?>
<a href="<?php echo BASE_URL; ?>/admin/usuarios/novo" class="btn-add"><i class="fas fa-user-plus"></i> Novo Usuário</a>
<?php endif; ?>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th>E-mail</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="action-btns">
                        <?php if ($is_master || $_SESSION['user_id'] == $user['id']): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/usuarios/editar/<?php echo $user['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                        <?php endif; ?>

                        <?php if ($is_master && $_SESSION['user_id'] != $user['id']): ?>
                            <form action="<?php echo BASE_URL; ?>/admin/usuarios/deletar" method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Deseja excluir este usuário?')"><i class="fas fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
