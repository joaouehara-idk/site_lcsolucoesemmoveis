<section id="tab-perfil">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Minha Conta
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Gerencie suas informacoes pessoais
        </span>
    </h1>
</div>

<div class="perfil-grid">

<div class="perfil-card" id="perfilInfoCard">
    <div class="perfil-card-header">
        <i class="ph-bold ph-user" style="font-size:18px;"></i>
        Informacoes Pessoais
    </div>
    <form id="perfilForm" onsubmit="perfilSalvar(event)">
        <div class="perfil-field">
            <label>Nome</label>
            <input type="text" id="perfNome" required>
        </div>
        <div class="perfil-field">
            <label>Email</label>
            <input type="email" id="perfEmail" required>
        </div>
        <div class="perfil-field" style="grid-column:1/-1;">
            <label>Funcao</label>
            <input type="text" id="perfRole" disabled style="color:var(--gold);font-weight:600;">
        </div>
        <div class="perfil-field" style="grid-column:1/-1;">
            <label>Membro desde</label>
            <input type="text" id="perfCreated" disabled style="color:var(--text-muted);">
        </div>
        <div style="grid-column:1/-1;display:flex;align-items:center;gap:10px;margin-top:4px;">
            <button type="submit" class="btn-primary" id="perfSaveBtn">Salvar Alteracoes</button>
            <span id="perfStatus" style="font-size:13px;"></span>
        </div>
    </form>
</div>

<div class="perfil-card" id="perfilPasswordCard">
    <div class="perfil-card-header">
        <i class="ph-bold ph-lock" style="font-size:18px;"></i>
        Alterar Senha
    </div>
    <form id="perfilPasswordForm" onsubmit="perfilPassword(event)">
        <div class="perfil-field" style="grid-column:1/-1;">
            <label>Senha Atual</label>
            <input type="password" id="perfSenhaAtual" required placeholder="Digite sua senha atual">
        </div>
        <div class="perfil-field">
            <label>Nova Senha</label>
            <input type="password" id="perfNovaSenha" required placeholder="Minimo 4 caracteres" minlength="4">
        </div>
        <div class="perfil-field">
            <label>Confirmar Nova Senha</label>
            <input type="password" id="perfConfirmSenha" required placeholder="Repita a nova senha">
        </div>
        <div class="perfil-field" style="grid-column:1/-1;display:flex;align-items:center;gap:10px;margin-top:4px;">
            <button type="submit" class="btn-primary" id="perfPassBtn" style="background:linear-gradient(135deg,#2ecc71,#27ae60)!important;color:#000!important;">Alterar Senha</button>
            <span id="perfPassStatus" style="font-size:13px;"></span>
        </div>
    </form>
</div>

</div>

<script>
function perfilCarregar() {
    fetch('api/perfil.php?action=info')
    .then(function(r){return r.json()})
    .then(function(d){
        if (d.success && d.user) {
            document.getElementById('perfNome').value = d.user.nome || '';
            document.getElementById('perfEmail').value = d.user.email || '';
            document.getElementById('perfRole').value = d.user.role === 'admin' ? 'Administrador' : 'Usuario';
            document.getElementById('perfCreated').value = d.user.created_at ? new Date(d.user.created_at).toLocaleDateString('pt-BR', {year:'numeric',month:'long',day:'numeric'}) : '-';
        }
    });
}

function perfilSalvar(e) {
    e.preventDefault();
    var btn = document.getElementById('perfSaveBtn');
    var status = document.getElementById('perfStatus');
    btn.disabled = true;
    status.innerHTML = 'Salvando...';
    fetch('api/perfil.php?action=update', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            nome: document.getElementById('perfNome').value.trim(),
            email: document.getElementById('perfEmail').value.trim()
        })
    })
    .then(function(r){return r.json()})
    .then(function(d){
        btn.disabled = false;
        if (d.success) {
            status.innerHTML = '<span style="color:#2ecc71;">Perfil atualizado!</span>';
            setTimeout(function(){status.innerHTML='';}, 3000);
        } else {
            status.innerHTML = '<span style="color:#e94560;">' + (d.error||'Erro') + '</span>';
        }
    })
    .catch(function(){
        btn.disabled = false;
        status.innerHTML = '<span style="color:#e94560;">Erro de conexao</span>';
    });
}

function perfilPassword(e) {
    e.preventDefault();
    var btn = document.getElementById('perfPassBtn');
    var status = document.getElementById('perfPassStatus');
    var nova = document.getElementById('perfNovaSenha').value;
    var conf = document.getElementById('perfConfirmSenha').value;

    if (nova !== conf) {
        status.innerHTML = '<span style="color:#e94560;">Senhas nao conferem</span>';
        return;
    }
    if (nova.length < 4) {
        status.innerHTML = '<span style="color:#e94560;">Minimo 4 caracteres</span>';
        return;
    }

    btn.disabled = true;
    status.innerHTML = 'Alterando...';
    fetch('api/perfil.php?action=password', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            current_password: document.getElementById('perfSenhaAtual').value,
            new_password: nova,
            confirm_password: conf
        })
    })
    .then(function(r){return r.json()})
    .then(function(d){
        btn.disabled = false;
        if (d.success) {
            status.innerHTML = '<span style="color:#2ecc71;">Senha alterada com sucesso!</span>';
            document.getElementById('perfSenhaAtual').value = '';
            document.getElementById('perfNovaSenha').value = '';
            document.getElementById('perfConfirmSenha').value = '';
            setTimeout(function(){status.innerHTML='';}, 3000);
        } else {
            status.innerHTML = '<span style="color:#e94560;">' + (d.error||'Erro') + '</span>';
        }
    })
    .catch(function(){
        btn.disabled = false;
        status.innerHTML = '<span style="color:#e94560;">Erro de conexao</span>';
    });
}

perfilCarregar();
</script>

</section>
