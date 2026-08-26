<h1>Novo Projeto</h1>

<div class="card" style="margin-bottom: 20px; background: var(--accent-surface); border: 1px dashed var(--accent); border-radius: 16px;">
    <h3 style="color: var(--accent); margin-bottom: 15px; font-family: 'DM Serif Display', serif; font-weight: 400;"><i class="fas fa-robot"></i> Assistente de Portfólio IA</h3>
    <div class="form-group" style="display: flex; gap: 10px;">
        <input type="text" id="ai-topic" class="form-control" placeholder="Descreva o projeto (ex: Área gourmet com ilha em mármore e madeira freijó)">
        <button type="button" id="btn-ai-generate" class="btn-save" style="min-width: 150px;">Gerar Info</button>
    </div>
    <p id="ai-status" style="margin-top: 10px; font-size: 0.85em; color: var(--ink-muted); display: none;">IA trabalhando no seu projeto...</p>
</div>

<div class="card">
    <form action="<?php echo BASE_URL; ?>/admin/portfolio/salvar" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="titulo">Nome do Projeto</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigável)</label>
            <input type="text" name="slug" id="slug" class="form-control">
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" placeholder="ex: Cozinhas, Dormitórios">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="imagem_capa">Imagem de Capa (Principal)</label>
            <input type="file" name="imagem_capa" id="imagem_capa" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <label for="video_capa">Video de Capa (Hover-play — opcional)</label>
            <input type="file" name="video_capa" id="video_capa" class="form-control" accept="video/mp4,video/webm">
            <small style="color: #888;">Envie um vídeo curto (MP4/WebM, máx ~5MB). Ele toca ao passar o mouse sobre o card.</small>
        </div>

        <div class="form-group">
            <label for="galeria">Galeria de Imagens (Selecione várias)</label>
            <input type="file" name="galeria[]" id="galeria" class="form-control" multiple>
            <small style="color: #888;">Você pode selecionar várias imagens de uma vez para este projeto.</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="destaque" value="1"> Marcar como Destaque na Home
            </label>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save">Salvar Projeto</button>
            <a href="<?php echo BASE_URL; ?>/admin/portfolio" style="color: #ccc; margin-left: 20px;">Cancelar</a>
        </div>
    </form>
</div>

<script>
// Auto-slug generator
document.getElementById('titulo').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, "")
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
    document.getElementById('slug').value = slug;
});

// AI Generation Logic
document.getElementById('btn-ai-generate').addEventListener('click', async function() {
    const topic = document.getElementById('ai-topic').value;
    const status = document.getElementById('ai-status');
    const btn = this;

    if (!topic) {
        alert('Por favor, descreva o projeto.');
        return;
    }

    btn.disabled = true;
    status.style.display = 'block';
    status.textContent = 'Aguarde, a IA está criando o conteúdo...';

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/admin/portfolio/ia-gerar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'topic=' + encodeURIComponent(topic)
        });

        const data = await response.json();

        if (data.error) {
            alert(data.error);
        } else {
            document.getElementById('titulo').value = data.title;
            document.getElementById('descricao').value = data.description;
            
            document.getElementById('titulo').dispatchEvent(new Event('input'));
            
            status.textContent = 'Informações geradas com sucesso!';
            status.style.color = 'var(--success, #4A7C59)';
        }
    } catch (error) {
        console.error(error);
        alert('Erro ao conectar com o servidor.');
    } finally {
        btn.disabled = false;
    }
});
</script>
