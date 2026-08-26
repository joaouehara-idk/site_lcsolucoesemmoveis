<h1>Gerenciar Portfólio</h1>
<a href="<?php echo BASE_URL; ?>/admin/portfolio/novo" class="btn-add"><i class="fas fa-plus"></i> Novo Projeto</a>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Categoria</th>
                <th>Destaque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projetos as $projeto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($projeto['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($projeto['categoria']); ?></td>
                    <td><?php echo $projeto['destaque'] ? 'Sim' : 'Não'; ?></td>
                    <td class="action-btns">
                        <a href="<?php echo BASE_URL; ?>/admin/portfolio/editar/<?php echo $projeto['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                        <form action="<?php echo BASE_URL; ?>/admin/portfolio/deletar" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo $projeto['id']; ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Deseja excluir este projeto?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
