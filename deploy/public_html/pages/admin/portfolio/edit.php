<h1>Editar Projeto</h1>

<div class="card">
    <form action="<?php echo BASE_URL; ?>/admin/portfolio/atualizar" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo $projeto['id']; ?>">
        
        <div class="form-group">
            <label for="titulo">Nome do Projeto</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo htmlspecialchars($projeto['titulo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigável)</label>
            <input type="text" name="slug" id="slug" class="form-control" value="<?php echo htmlspecialchars($projeto['slug']); ?>">
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" value="<?php echo htmlspecialchars($projeto['categoria']); ?>">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($projeto['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagem de Capa Atual</label><br>
            <?php if (!empty($projeto['imagem_capa'])): ?>
                <?php $imgUrl = (strpos($projeto['imagem_capa'], 'http') === 0) ? $projeto['imagem_capa'] : BASE_URL . $projeto['imagem_capa']; ?>
                <img src="<?php echo $imgUrl; ?>" style="width: 150px; border-radius: 10px; margin-bottom: 10px; border: 1px solid rgba(26,23,20,0.08);">
            <?php endif; ?>
            <input type="file" name="imagem_capa" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <label>Video de Capa Atual</label><br>
            <?php if (!empty($projeto['video_capa'])): ?>
                <?php $vidUrl = (strpos($projeto['video_capa'], 'http') === 0) ? $projeto['video_capa'] : BASE_URL . $projeto['video_capa']; ?>
                <video src="<?php echo $vidUrl; ?>" style="width: 150px; border-radius: 10px; margin-bottom: 10px; border: 1px solid rgba(26,23,20,0.08);" controls></video>
            <?php endif; ?>
            <input type="file" name="video_capa" class="form-control" accept="video/mp4,video/webm">
            <small style="color: #888;">Envie um novo vídeo para substituir o atual (deixe em branco para manter).</small>
        </div>

        <div class="form-group">
            <label>Galeria de Imagens</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-bottom: 15px;">
                <?php if (!empty($projeto['imagens'])): ?>
                    <?php foreach ($projeto['imagens'] as $img): ?>
                        <div style="position: relative; border: 1px solid #444; border-radius: 8px; overflow: hidden;">
                            <img src="<?php echo BASE_URL . $img['caminho']; ?>" style="width: 100%; height: 100px; object-fit: cover;">
                            <button type="button" onclick="deleteImage(<?php echo $img['id']; ?>)" style="position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888; grid-column: 1/-1;">Nenhuma imagem na galeria.</p>
                <?php endif; ?>
            </div>
            <label for="galeria">Adicionar Imagens à Galeria</label>
            <input type="file" name="galeria[]" id="galeria" class="form-control" multiple>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="destaque" value="1" <?php echo $projeto['destaque'] ? 'checked' : ''; ?>> Marcar como Destaque na Home
            </label>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save">Atualizar Projeto</button>
            <a href="<?php echo BASE_URL; ?>/admin/portfolio" style="color: #ccc; margin-left: 20px;">Cancelar</a>
        </div>
    </form>
</div>

<form id="delete-image-form" action="<?php echo BASE_URL; ?>/admin/portfolio/deletar-imagem" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="imagem_id" id="delete-image-id">
    <input type="hidden" name="projeto_id" value="<?php echo $projeto['id']; ?>">
</form>

<script>
function deleteImage(id) {
    if (confirm('Tem certeza que deseja remover esta imagem da galeria?')) {
        document.getElementById('delete-image-id').value = id;
        document.getElementById('delete-image-form').submit();
    }
}
</script>
