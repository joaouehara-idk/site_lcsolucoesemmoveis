<h1>Editar Post</h1>

<div class="card">
    <form action="<?php echo BASE_URL; ?>/admin/blog/atualizar" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
        
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo htmlspecialchars($post['titulo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigável)</label>
            <input type="text" name="slug" id="slug" class="form-control" value="<?php echo htmlspecialchars($post['slug']); ?>">
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" value="<?php echo htmlspecialchars($post['categoria'] ?? 'Móveis Planejados'); ?>">
        </div>

        <div class="form-group">
            <label for="resumo">Resumo (SEO)</label>
            <textarea name="resumo" id="resumo" class="form-control" rows="3"><?php echo htmlspecialchars($post['resumo'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="conteudo">Conteúdo</label>
            <textarea name="conteudo" id="conteudo" class="form-control" rows="10" required><?php echo htmlspecialchars($post['conteudo']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagem Atual</label><br>
            <?php if (!empty($post['imagem'])): ?>
                <?php $imgUrl = (strpos($post['imagem'], 'http') === 0) ? $post['imagem'] : BASE_URL . $post['imagem']; ?>
                <img src="<?php echo $imgUrl; ?>" style="width: 150px; border-radius: 10px; margin-bottom: 10px; border: 1px solid rgba(26,23,20,0.08);">
            <?php endif; ?>
            <input type="file" name="imagem" class="form-control">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="publicado" <?php echo $post['status'] == 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                <option value="rascunho" <?php echo $post['status'] == 'rascunho' ? 'selected' : ''; ?>>Rascunho</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save">Atualizar Post</button>
            <a href="<?php echo BASE_URL; ?>/admin/blog" style="color: #ccc; margin-left: 20px;">Cancelar</a>
        </div>
    </form>
</div>
