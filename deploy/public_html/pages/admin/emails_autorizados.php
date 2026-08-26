<div class="card">
    <h1>Emails Autorizados</h1>
    <p style="margin-bottom: 20px; color: #888;">Apenas emails nesta lista podem criar contas no painel administrativo.</p>

    <form method="POST" action="<?php echo BASE_URL; ?>/admin/emails-autorizados/salvar">
        <?php echo csrf_field(); ?>
        <div id="emails-list">
            <?php if (empty($emails)): ?>
                <div class="email-row" style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="email" name="emails[]" class="form-control" placeholder="email@exemplo.com" required>
                </div>
            <?php else: ?>
                <?php foreach ($emails as $e): ?>
                <div class="email-row" style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="email" name="emails[]" class="form-control" value="<?php echo htmlspecialchars($e); ?>" required>
                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()" style="background:#e74c3c; color:#fff; border:none; padding:8px 15px; border-radius:5px; cursor:pointer;">✕</button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" onclick="addEmail()" style="background:transparent; color:var(--accent); border:1px dashed var(--accent); padding:10px 20px; border-radius:100px; cursor:pointer; margin-bottom:20px; font-family:inherit; font-size:13px; font-weight:500;">
            + Adicionar Email
        </button>

        <br>
        <button type="submit" class="btn-save">Salvar</button>
    </form>
</div>

<script>
function addEmail() {
    const div = document.createElement('div');
    div.className = 'email-row';
    div.style.cssText = 'display:flex; gap:10px; margin-bottom:10px;';
    div.innerHTML = '<input type="email" name="emails[]" class="form-control" placeholder="email@exemplo.com" required>' +
        '<button type="button" class="btn-remove" onclick="this.parentElement.remove()" style="background:#e74c3c; color:#fff; border:none; padding:8px 15px; border-radius:5px; cursor:pointer;">✕</button>';
    document.getElementById('emails-list').appendChild(div);
}
</script>
