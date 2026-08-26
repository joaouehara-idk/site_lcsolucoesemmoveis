<section id="tab-clientes">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Clientes
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Leads, contatos e projetos
        </span>
    </h1>
</div>

<!-- Cliente Stats -->
<div class="cards" id="clienteStats" style="margin-bottom:16px;">
    <div class="card"><div class="num" id="csTotal">0</div><div class="label">Total Clientes</div></div>
    <div class="card"><div class="num" id="csLeads" style="color:#f39c12;">0</div><div class="label">Leads</div></div>
    <div class="card"><div class="num" id="csContato" style="color:#3498db;">0</div><div class="label">Em Contato</div></div>
    <div class="card"><div class="num" id="csProjeto" style="color:#9b59b6;">0</div><div class="label">Em Projeto</div></div>
    <div class="card"><div class="num" id="csConcluidos" style="color:#2ecc71;">0</div><div class="label">Concluídos</div></div>
    <div class="card"><div class="num" id="csReceita" style="color:var(--gold);">R$ 0</div><div class="label">Receita Total</div></div>
</div>

<div class="exp-controls" style="margin-bottom:16px;">
    <div class="row">
        <label>Filtro</label>
        <select id="clienteFiltro" onchange="cliCarregar()" style="min-width:140px;">
            <option value="">Todos</option>
            <option value="lead">Leads</option>
            <option value="contato">Em Contato</option>
            <option value="projeto">Em Projeto</option>
            <option value="concluido">Concluídos</option>
            <option value="inativo">Inativos</option>
        </select>
        <input type="text" id="clienteBusca" placeholder="Buscar cliente..." onkeydown="if(event.key==='Enter') cliCarregar()">
        <button class="btn-primary" onclick="cliCarregar()">Carregar</button>
        <button class="btn-primary" onclick="cliAutoImport(event)" style="background:linear-gradient(135deg,#27ae60,#1e8449)!important;color:#000!important;">Importar do Email</button>
        <button class="btn-primary" onclick="cliAbrirForm()" style="background:linear-gradient(135deg,#3498db,#2980b9)!important;color:#000!important;">+ Novo Cliente</button>
    </div>
</div>

<!-- Cliente Form Modal -->
<div id="clienteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:28px;max-width:560px;width:90%;max-height:90vh;overflow-y:auto;border:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-family:var(--font-heading);font-size:18px;color:var(--gold);font-weight:700;" id="clienteModalTitle">Novo Cliente</h3>
            <button onclick="cliFecharForm()" style="background:none;border:none;color:var(--text-dim);font-size:24px;cursor:pointer;">&times;</button>
        </div>
        <input type="hidden" id="cliFormId" value="0">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="grid-column:1/-1;"><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Razão Social *</label><input type="text" id="cliRazao" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Nome Fantasia</label><input type="text" id="cliFantasia" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">CPF/CNPJ</label><input type="text" id="cliDoc" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Telefone</label><input type="text" id="cliTel" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Email</label><input type="email" id="cliEmail" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Status</label>
                <select id="cliStatus" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;">
                    <option value="lead">Lead</option>
                    <option value="contato">Em Contato</option>
                    <option value="projeto">Em Projeto</option>
                    <option value="concluido">Concluído</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Origem</label>
                <select id="cliOrigem" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;">
                    <option value="manual">Manual</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="indicacao">Indicação</option>
                    <option value="site">Site</option>
                    <option value="importado">Importado</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div style="grid-column:1/-1;"><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Observações</label><textarea id="cliObs" rows="2" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;resize:vertical;font-family:var(--font-body);font-size:13px;"></textarea></div>
        </div>
        <div style="display:flex;gap:10px;margin-top:18px;">
            <button class="btn-primary" onclick="cliSalvar()" style="flex:1;justify-content:center;padding:12px;">Salvar Cliente</button>
            <button class="btn-primary" onclick="cliFecharForm()" style="flex:1;justify-content:center;padding:12px;background:transparent!important;color:var(--text-muted)!important;border:1px solid var(--border)!important;">Cancelar</button>
        </div>
    </div>
</div>

<div class="exp-table-wrap">
    <div class="exp-toolbar" id="clienteToolbar" style="display:none;">
        <div class="left">
            <span class="count" id="clienteCount"></span>
        </div>
        <div>
            <span style="font-size:12px;color:var(--text-dim);">Clique em um cliente para ver detalhes e projetos</span>
        </div>
    </div>
    <div id="clienteTableContainer">
        <div class="exp-empty"><h3>Carregue a lista de clientes</h3></div>
    </div>
</div>

<!-- Cliente Detail Modal -->
<div id="clienteDetailModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:28px;max-width:700px;width:90%;max-height:90vh;overflow-y:auto;border:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-family:var(--font-heading);font-size:18px;color:var(--text);font-weight:700;" id="detRazao"></h3>
                <span id="detStatus" class="badge" style="margin-top:4px;"></span>
            </div>
            <div style="display:flex;gap:6px;">
                <button class="btn-primary" onclick="waAbrir(document.getElementById('detTel').textContent)" style="padding:6px 14px;font-size:11px;background:linear-gradient(135deg,#25D366,#128C7E)!important;color:#fff!important;">WhatsApp</button>
                <button class="btn-primary" onclick="cliEditar()" style="padding:6px 14px;font-size:11px;">Editar</button>
                <button class="btn-primary" onclick="cliExcluirDetalhe()" style="padding:6px 14px;font-size:11px;background:rgba(231,76,60,0.2)!important;color:#e74c3c!important;border:1px solid rgba(231,76,60,0.3)!important;">Excluir</button>
                <button onclick="cliFecharDetalhes()" style="background:none;border:none;color:var(--text-dim);font-size:24px;cursor:pointer;">&times;</button>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:16px;padding:12px;background:rgba(0,0,0,0.3);border-radius:var(--radius-md);">
            <div>📞 <span id="detTel">-</span></div>
            <div>✉️ <span id="detEmail">-</span></div>
            <div>🏷️ <span id="detDoc">-</span></div>
            <div>📅 <span id="detContato">-</span></div>
            <div>🔄 <span id="detOrigem">-</span></div>
            <div>💰 <span id="detGasto">-</span></div>
        </div>
        <div id="detObs" style="font-size:13px;color:var(--text-muted);margin-bottom:16px;padding:12px;background:rgba(0,0,0,0.3);border-radius:var(--radius-md);display:none;"></div>

        <h4 style="font-family:var(--font-heading);font-size:13px;color:var(--gold);margin-bottom:10px;font-weight:700;">Projetos</h4>
        <div id="detProjetos" style="font-size:13px;color:var(--text-dim);">Nenhum projeto cadastrado.</div>
        <div style="margin-top:12px;">
            <button class="btn-primary" onclick="projAbrirForm()" style="background:linear-gradient(135deg,#3498db,#2980b9)!important;color:#000!important;">+ Novo Projeto</button>
        </div>
    </div>
</div>

<script>
function waLink(tel) {
    if (!tel) return '';
    return 'https://wa.me/55' + tel.replace(/\D/g, '');
}
function waAbrir(tel) {
    if (!tel || tel === '-') { alert('Cliente sem telefone.'); return; }
    window.open(waLink(tel), '_blank');
}

function esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

let cliAllData = [];
let cliCurrentId = 0;

function cliCarregar() {
    var status = document.getElementById('clienteFiltro').value;
    var q = document.getElementById('clienteBusca').value.trim();
    var url = 'api/clientes.php?action=list&limit=500';
    if (status) url += '&status=' + encodeURIComponent(status);
    if (q) url += '&q=' + encodeURIComponent(q);

    fetch('api/clientes.php?action=stats').then(function(r){return r.json()}).then(function(st){
        if (st.success) {
            document.getElementById('csTotal').textContent = st.data.total_clientes;
            document.getElementById('csLeads').textContent = st.data.leads;
            document.getElementById('csContato').textContent = st.data.contato;
            document.getElementById('csProjeto').textContent = st.data.em_projeto;
            document.getElementById('csConcluidos').textContent = st.data.concluidos;
            document.getElementById('csReceita').textContent = 'R$ ' + parseFloat(st.data.receita).toLocaleString('pt-BR', {minimumFractionDigits:2});
        }
    });

    document.getElementById('clienteTableContainer').innerHTML = '<div class="exp-empty"><h3>Carregando...</h3></div>';
    fetch(url).then(function(r){return r.json()}).then(function(d){
        cliAllData = d.records || [];
        document.getElementById('clienteToolbar').style.display = cliAllData.length ? 'flex' : 'none';
        document.getElementById('clienteCount').textContent = d.total + ' clientes';

        if (!cliAllData.length) {
            document.getElementById('clienteTableContainer').innerHTML = '<div class="exp-empty"><h3>Nenhum cliente encontrado</h3><p>Clique em "+ Novo Cliente" para adicionar ou "Importar do Email".</p></div>';
            return;
        }

        var html = '<div style="overflow-x:auto;"><table><thead><tr>' +
            '<th>Razão Social</th><th>Fantasia</th><th>Contato</th><th>Email</th><th>Status</th><th>Origem</th><th>Projetos</th><th>Desde</th><th></th>' +
            '</tr></thead><tbody>';

        cliAllData.forEach(function(r) {
            var statClass = r.status === 'lead' ? 'badge-inapta' : r.status === 'contato' ? 'badge-outro' : r.status === 'projeto' ? 'badge-baixada' : r.status === 'concluido' ? 'badge-ativo' : 'badge-outro';
            var statLabel = r.status === 'lead' ? 'Lead' : r.status === 'contato' ? 'Contato' : r.status === 'projeto' ? 'Projeto' : r.status === 'concluido' ? 'Concluído' : 'Inativo';
            var tel = esc(r.telefone) || '-';
            var telRaw = (r.telefone || '').replace(/\D/g, '');
            var waBtn = telRaw ? '<a href="https://wa.me/55' + telRaw + '" target="_blank" title="WhatsApp" style="color:#25D366;text-decoration:none;font-size:16px;"><i class="ph-bold ph-whatsapp-logo"></i></a>' : '';
            html += '<tr style="cursor:pointer;">' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')"><strong>' + esc(r.razao_social || 'N/I') + '</strong></td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')">' + esc(r.nome_fantasia || '-') + '</td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')">' + tel + ' ' + waBtn + '</td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')" style="font-size:11px;">' + esc(r.email || '-') + '</td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')"><span class="badge ' + statClass + '">' + statLabel + '</span></td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')" style="font-size:11px;">' + esc(r.origem) + '</td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')">' + (r.total_projetos || 0) + '</td>' +
                '<td onclick="cliAbrirDetalhes(' + r.id + ')" style="font-size:11px;">' + esc(r.contato_inicial || '-') + '</td>' +
                '<td><button onclick="event.stopPropagation();cliExcluirLinha(' + r.id + ')" style="background:none;border:none;color:#e74c3c;cursor:pointer;font-size:16px;padding:4px;" title="Excluir parceiro"><i class="ph-bold ph-trash"></i></button></td></tr>';
        });
        html += '</tbody></table></div>';
        document.getElementById('clienteTableContainer').innerHTML = html;
    });
}

function cliAbrirDetalhes(id) {
    cliCurrentId = id;
    fetch('api/clientes.php?action=get&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (!d.success) return;
        var c = d.record;
        document.getElementById('detRazao').textContent = c.razao_social || 'N/I';
        var statLabel = c.status === 'lead' ? 'Lead' : c.status === 'contato' ? 'Contato' : c.status === 'projeto' ? 'Projeto' : c.status === 'concluido' ? 'Concluído' : 'Inativo';
        var statClass = c.status === 'lead' ? 'badge-inapta' : c.status === 'contato' ? 'badge-outro' : c.status === 'projeto' ? 'badge-baixada' : c.status === 'concluido' ? 'badge-ativo' : 'badge-outro';
        document.getElementById('detStatus').textContent = statLabel;
        document.getElementById('detStatus').className = 'badge ' + statClass;
        document.getElementById('detTel').textContent = c.telefone || '-';
        document.getElementById('detEmail').textContent = c.email || '-';
        document.getElementById('detDoc').textContent = c.cnpj || c.cpf || '-';
        document.getElementById('detContato').textContent = c.contato_inicial || '-';
        document.getElementById('detOrigem').textContent = c.origem || '-';
        document.getElementById('detGasto').textContent = c.total_gasto ? 'R$ ' + parseFloat(c.total_gasto).toLocaleString('pt-BR', {minimumFractionDigits:2}) : '-';
        var obs = document.getElementById('detObs');
        if (c.observacoes) { obs.textContent = c.observacoes; obs.style.display = 'block'; }
        else obs.style.display = 'none';
        document.getElementById('clienteDetailModal').style.display = 'flex';
        projCarregar(id);
    });
}

function cliFecharDetalhes() {
    document.getElementById('clienteDetailModal').style.display = 'none';
}

function cliEditar() {
    if (!cliCurrentId) return;
    fetch('api/clientes.php?action=get&id=' + cliCurrentId).then(function(r){return r.json()}).then(function(d){
        if (!d.success) return;
        var c = d.record;
        document.getElementById('cliFormId').value = c.id;
        document.getElementById('cliRazao').value = c.razao_social || '';
        document.getElementById('cliFantasia').value = c.nome_fantasia || '';
        document.getElementById('cliDoc').value = c.cnpj || c.cpf || '';
        document.getElementById('cliTel').value = c.telefone || '';
        document.getElementById('cliEmail').value = c.email || '';
        document.getElementById('cliStatus').value = c.status || 'lead';
        document.getElementById('cliOrigem').value = c.origem || 'manual';
        document.getElementById('cliObs').value = c.observacoes || '';
        document.getElementById('clienteModalTitle').textContent = 'Editar Cliente';
        document.getElementById('clienteModal').style.display = 'flex';
        cliFecharDetalhes();
    });
}

function cliAbrirForm() {
    document.getElementById('cliFormId').value = 0;
    ['cliRazao','cliFantasia','cliDoc','cliTel','cliEmail'].forEach(function(id){ document.getElementById(id).value = ''; });
    document.getElementById('cliStatus').value = 'lead';
    document.getElementById('cliOrigem').value = 'manual';
    document.getElementById('cliObs').value = '';
    document.getElementById('clienteModalTitle').textContent = 'Novo Cliente';
    document.getElementById('clienteModal').style.display = 'flex';
}

function cliFecharForm() {
    document.getElementById('clienteModal').style.display = 'none';
}

function cliSalvar() {
    var razao = document.getElementById('cliRazao').value.trim();
    if (!razao) { alert('Informe a razão social.'); return; }
    var data = {
        id: parseInt(document.getElementById('cliFormId').value) || 0,
        razao_social: razao,
        nome_fantasia: document.getElementById('cliFantasia').value.trim(),
        cnpj: document.getElementById('cliDoc').value.trim(),
        telefone: document.getElementById('cliTel').value.trim(),
        email: document.getElementById('cliEmail').value.trim(),
        status: document.getElementById('cliStatus').value,
        origem: document.getElementById('cliOrigem').value,
        contato_inicial: new Date().toISOString().split('T')[0],
        observacoes: document.getElementById('cliObs').value.trim(),
    };
    fetch('api/clientes.php?action=save', {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
    }).then(function(r){return r.json()}).then(function(d) {
        if (d.success) { cliFecharForm(); cliCarregar(); }
        else alert(d.error || 'Erro ao salvar');
    });
}

function cliExcluir(id) {
    if (!confirm('Tem certeza que deseja excluir este parceiro?')) return;
    fetch('api/clientes.php?action=delete&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (d.success) { cliFecharDetalhes(); cliCarregar(); }
        else alert(d.error || 'Erro ao excluir');
    });
}

function cliExcluirDetalhe() {
    if (cliCurrentId) cliExcluir(cliCurrentId);
}

function cliExcluirLinha(id) {
    cliExcluir(id);
}

function cliAutoImport(event) {
    var btn = event ? event.target : document.querySelector('#clienteToolbar .btn-primary');
    if (!btn) return;
    btn.textContent = 'Importando...';
    fetch('api/clientes.php?action=auto_import').then(function(r){return r.json()}).then(function(d){
        btn.textContent = 'Importar do Email';
        alert(d.message);
        cliCarregar();
    });
}

cliCarregar();

/* PROJETOS */
var projTargetId = 0;

function projCarregar(clienteId) {
    projTargetId = clienteId || 0;
    fetch('api/projetos.php?action=list&cliente_id=' + clienteId + '&limit=50').then(function(r){return r.json()}).then(function(d){
        var container = document.getElementById('detProjetos');
        if (!d.success || !d.records || !d.records.length) {
            container.innerHTML = '<div style="font-size:13px;color:var(--text-dim);">Nenhum projeto cadastrado para este cliente.</div>';
            return;
        }
        var html = '<div style="overflow-x:auto;"><table><thead><tr><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Status</th><th>Início</th><th>Conclusão</th><th></th></tr></thead><tbody>';
        d.records.forEach(function(p) {
            var st = p.status === 'concluido' ? 'badge-ativo' : p.status === 'cancelado' ? 'badge-baixada' : 'badge-outro';
            html += '<tr><td>' + esc(p.descricao) + '</td><td style="font-size:11px;">' + esc(p.tipo.replace(/_/g,' ')) + '</td><td>' + (p.valor ? 'R$ ' + parseFloat(p.valor).toLocaleString('pt-BR', {minimumFractionDigits:2}) : '-') + '</td><td><span class="badge ' + st + '">' + esc(p.status) + '</span></td><td style="font-size:11px;">' + esc(p.data_inicio || '-') + '</td><td style="font-size:11px;">' + esc(p.data_conclusao || '-') + '</td><td><button onclick="projExcluir(' + p.id + ')" style="background:none;border:none;color:var(--text-dim);cursor:pointer;">&times;</button></td></tr>';
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    });
}

function projAbrirForm() {
    if (!cliCurrentId) return;
    document.getElementById('projFormId').value = 0;
    document.getElementById('projDescricao').value = '';
    document.getElementById('projTipo').value = 'moveis_planejados';
    document.getElementById('projValor').value = '';
    document.getElementById('projDataInicio').value = new Date().toISOString().split('T')[0];
    document.getElementById('projDataConclusao').value = '';
    document.getElementById('projStatusProj').value = 'orcamento';
    document.getElementById('projObs').value = '';
    document.getElementById('projModalTitle').textContent = 'Novo Projeto';
    document.getElementById('projModal').style.display = 'flex';
}

function projFecharForm() {
    document.getElementById('projModal').style.display = 'none';
}

function projSalvar() {
    var desc = document.getElementById('projDescricao').value.trim();
    if (!desc) { alert('Descreva o projeto.'); return; }
    var data = {
        id: parseInt(document.getElementById('projFormId').value) || 0,
        cliente_id: cliCurrentId,
        descricao: desc,
        tipo: document.getElementById('projTipo').value,
        valor: parseFloat(document.getElementById('projValor').value.replace(/[^0-9.,]/g,'').replace(',','.')) || null,
        data_inicio: document.getElementById('projDataInicio').value || null,
        data_conclusao: document.getElementById('projDataConclusao').value || null,
        status: document.getElementById('projStatusProj').value,
        observacoes: document.getElementById('projObs').value.trim(),
    };
    fetch('api/projetos.php?action=save', {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
    }).then(function(r){return r.json()}).then(function(d){
        if (d.success) { projFecharForm(); projCarregar(cliCurrentId); cliAbrirDetalhes(cliCurrentId); cliCarregar(); }
        else alert(d.error || 'Erro ao salvar projeto');
    });
}

function projExcluir(id) {
    if (!confirm('Remover este projeto?')) return;
    fetch('api/projetos.php?action=delete&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (d.success) projCarregar(cliCurrentId);
    });
}

// Close modals on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.id === 'clienteModal') document.getElementById('clienteModal').style.display = 'none';
    if (e.target.id === 'clienteDetailModal') document.getElementById('clienteDetailModal').style.display = 'none';
    if (e.target.id === 'projModal') document.getElementById('projModal').style.display = 'none';
});
</script>

<!-- Projeto Form Modal -->
<div id="projModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:28px;max-width:520px;width:90%;border:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-family:var(--font-heading);font-size:18px;color:var(--gold);font-weight:700;" id="projModalTitle">Novo Projeto</h3>
            <button onclick="projFecharForm()" style="background:none;border:none;color:var(--text-dim);font-size:24px;cursor:pointer;">&times;</button>
        </div>
        <input type="hidden" id="projFormId" value="0">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="grid-column:1/-1;"><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Descrição *</label><input type="text" id="projDescricao" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Tipo</label>
                <select id="projTipo" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;">
                    <option value="moveis_planejados">Móveis Planejados</option>
                    <option value="cozinha">Cozinha</option>
                    <option value="dormitorio">Dormitório</option>
                    <option value="sala">Sala</option>
                    <option value="escritorio">Escritório</option>
                    <option value="closet">Closet</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Valor (R$)</label><input type="text" id="projValor" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;" placeholder="0,00"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Data Início</label><input type="date" id="projDataInicio" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Data Conclusão</label><input type="date" id="projDataConclusao" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;"></div>
            <div><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Status</label>
                <select id="projStatusProj" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;">
                    <option value="orcamento">Orçamento</option>
                    <option value="aprovado">Aprovado</option>
                    <option value="producao">Produção</option>
                    <option value="instalacao">Instalação</option>
                    <option value="concluido">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div style="grid-column:1/-1;"><label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Observações</label><textarea id="projObs" rows="2" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;resize:vertical;font-family:var(--font-body);font-size:13px;"></textarea></div>
        </div>
        <div style="display:flex;gap:10px;margin-top:18px;">
            <button class="btn-primary" onclick="projSalvar()" style="flex:1;justify-content:center;padding:12px;">Salvar Projeto</button>
            <button class="btn-primary" onclick="projFecharForm()" style="flex:1;justify-content:center;padding:12px;background:transparent!important;color:var(--text-muted)!important;border:1px solid var(--border)!important;">Cancelar</button>
        </div>
    </div>
</div>

</section>
<!-- PROJETOS -->
