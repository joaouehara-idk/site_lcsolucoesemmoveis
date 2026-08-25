<h1>Novo Post</h1>

<div class="card" style="margin-bottom: 20px; background: #222; border: 1px solid #00ff8833;">
    <h3 style="color: #00ff88; margin-bottom: 15px;"><i class="fas fa-robot"></i> Assistente de Escrita IA (Gemini)</h3>
    <div class="form-group" style="display: flex; gap: 10px;">
        <input type="text" id="ai-topic" class="form-control" placeholder="Sobre o que você quer escrever hoje? (ex: Dicas para cozinhas pequenas)">
        <button type="button" id="btn-ai-generate" class="btn-save" style="background: #00ff88; color: #000; min-width: 150px;">Gerar Post</button>
    </div>
    <p id="ai-status" style="margin-top: 10px; font-size: 0.9em; color: #888; display: none;">Aguarde, a IA está pensando...</p>
</div>

<div class="card">
    <form action="<?php echo BASE_URL; ?>/admin/blog/salvar" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigável)</label>
            <input type="text" name="slug" id="slug" class="form-control" placeholder="ex: tendencia-cozinha-2026">
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" placeholder="ex: Cozinhas, Dicas..." value="Móveis Planejados">
        </div>

        <div class="form-group">
            <label for="resumo">Resumo (SEO)</label>
            <textarea name="resumo" id="resumo" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="conteudo">Conteúdo</label>
            <textarea name="conteudo" id="conteudo" class="form-control" rows="10" required></textarea>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem de Destaque</label>
            <input type="file" name="imagem" id="imagem" class="form-control">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="publicado">Publicado</option>
                <option value="rascunho">Rascunho</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save">Salvar Post</button>
            <a href="<?php echo BASE_URL; ?>/admin/blog" style="color: #ccc; margin-left: 20px;">Cancelar</a>
        </div>
    </form>
</div>

<script>
// Auto-slug generator
document.getElementById('titulo').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, "") // Remove acentos
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
        alert('Por favor, informe um tema.');
        return;
    }

    btn.disabled = true;
    status.style.display = 'block';
    status.textContent = 'Aguarde, a IA está escrevendo seu post...';

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/admin/blog/ia-gerar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'topic=' + encodeURIComponent(topic)
        });

        const data = await response.json();

        if (data.error) {
            alert(data.error);
        } else {
            document.getElementById('titulo').value = data.title;
            document.getElementById('resumo').value = data.summary;
            document.getElementById('conteudo').value = data.content;
            
            // Disparar evento de input para gerar o slug automaticamente
            document.getElementById('titulo').dispatchEvent(new Event('input'));
            
            status.textContent = 'Post gerado com sucesso!';
            status.style.color = '#00ff88';
        }
    } catch (error) {
        console.error(error);
        alert('Erro ao conectar com o servidor.');
    } finally {
        btn.disabled = false;
    }
});
</script>
