<h1>Gerenciar Blog</h1>
<a href="<?php echo BASE_URL; ?>/admin/blog/novo" class="btn-add"><i class="fas fa-plus"></i> Novo Post</a>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($post['status']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                    <td class="action-btns">
                        <a href="<?php echo BASE_URL; ?>/admin/blog/editar/<?php echo $post['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                        <form action="<?php echo BASE_URL; ?>/admin/blog/deletar" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Deseja excluir este post?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
