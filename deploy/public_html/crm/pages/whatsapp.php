<section id="tab-whatsapp">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        WhatsApp
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Disparo rapido para parceiros
        </span>
    </h1>
</div>

<div class="cards" style="margin-bottom:16px;">
    <div class="card"><div class="num" id="waStatus" style="color:#25D366;">walink</div><div class="label">Modo</div></div>
    <div class="card"><div class="num" id="waClientes" style="color:var(--gold);">0</div><div class="label">Clientes</div></div>
    <div class="card"><div class="num" id="waComTel" style="color:#2ecc71;">0</div><div class="label">Com WhatsApp</div></div>
</div>

<div class="exp-controls" style="margin-bottom:8px;">
    <div class="row">
        <div style="display:flex;gap:4px;background:rgba(0,0,0,0.3);border-radius:var(--radius-sm);padding:3px;">
            <button id="waModeClientes" class="wa-mode-btn active" onclick="waSetMode('clientes')">Parceiros CRM</button>
            <button id="waModeSegmentos" class="wa-mode-btn" onclick="waSetMode('segmentos')">Segmentos</button>
        </div>
        <label style="max-width:60px;">Template</label>
        <select id="waTemplate" onchange="waPreencherMsg()" style="min-width:150px;max-width:180px;">
            <option value="">Personalizada</option>
            <option value="boas_vindas">Boas Vindas</option>
            <option value="orcamento">Orcamento</option>
            <option value="acompanhamento">Acompanhamento</option>
            <option value="finalizacao">Finalizacao</option>
        </select>
    </div>
</div>

<div class="exp-controls" id="waRowCliente" style="margin-bottom:16px;">
    <div class="row">
        <label>Cliente</label>
        <select id="waCliente" style="min-width:220px;flex:1;">
            <option value="">Selecione um cliente...</option>
        </select>
    </div>
</div>

<div class="exp-controls" id="waRowSegmento" style="margin-bottom:16px;display:none;">
    <div class="row">
        <label>Segmento</label>
        <select id="waSegmento" onchange="waListarSegmento()" style="min-width:200px;flex:1;">
            <option value="">Selecione um segmento...</option>
            <option value="arquitetos_ms">Arquitetos</option>
            <option value="designers_interiores_ms">Designers de Interiores</option>
            <option value="designers_ms">Designers</option>
            <option value="construtoras_ms">Construtoras</option>
            <option value="imobiliarias_ms">Imobiliárias</option>
            <option value="engenheiros_ms">Engenheiros</option>
            <option value="lojas_marcenarias_ms">Lojas e Marcenarias</option>
            <option value="paisagismo_decoracao_ms">Paisagismo e Decoração</option>
        </select>
        <input type="text" id="waBuscaSeg" placeholder="Buscar..." onkeydown="if(event.key==='Enter') waListarSegmento()" style="min-width:150px;flex:0.5;">
        <button class="btn-primary" onclick="waListarSegmento()" style="padding:6px 14px;">Carregar</button>
        <span id="waSegCount" style="font-size:12px;color:var(--text-dim);white-space:nowrap;"></span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <!-- Painel de Mensagem -->
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:20px;border:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <i class="ph-bold ph-whatsapp-logo" style="color:#25D366;font-size:22px;"></i>
            <span style="font-weight:700;color:var(--text);font-size:14px;">Mensagem</span>
        </div>
        <div style="display:grid;gap:10px;">
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Numero</label>
                <div style="display:flex;gap:8px;align-items:center;margin-top:4px;">
                    <input type="text" id="waNumero" placeholder="(67) 99999-9999" readonly style="flex:1;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text-dim);outline:none;cursor:default;">
                    <label style="font-size:11px;color:var(--text-dim);cursor:pointer;display:flex;align-items:center;gap:4px;white-space:nowrap;" title="Adicionar 9\u00ba digito para celular">
                        <input type="checkbox" id="waToggle9" onchange="waAtualizarNumero()" style="accent-color:#f39c12;"> +9
                    </label>
                </div>
            </div>
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Mensagem</label>
                <textarea id="waMensagem" rows="6" placeholder="Selecione um cliente e um template..." style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-top:4px;resize:vertical;font-family:var(--font-body);font-size:13px;"></textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button id="waBtnIA" class="btn-primary" onclick="waGerarIA()" style="flex:1;justify-content:center;padding:10px;background:linear-gradient(135deg,#9b59b6,#8e44ad)!important;color:#fff!important;font-size:12px;">
                    <i class="ph-bold ph-magic-wand"></i> Gerar com IA
                </button>
                <button class="btn-primary" onclick="waEnviar()" style="flex:2;justify-content:center;padding:12px;background:linear-gradient(135deg,#25D366,#128C7E)!important;color:#fff!important;">
                    <i class="ph-bold ph-whatsapp-logo"></i> Abrir no WhatsApp
                </button>
            </div>
            <div id="waResult" style="display:none;padding:10px;border-radius:var(--radius-sm);font-size:13px;text-align:center;"></div>
        </div>
    </div>

    <!-- Clientes com WhatsApp -->
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:20px;border:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <i class="ph-bold ph-users-three" style="color:var(--gold);font-size:22px;"></i>
            <span style="font-weight:700;color:var(--text);font-size:14px;">Parceiros</span>
        </div>
        <input type="text" id="waBusca" placeholder="Buscar parceiro..." onkeyup="waListarClientes()" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,.5);color:var(--text);outline:none;margin-bottom:10px;">
        <div id="waClientesLista" style="max-height:420px;overflow-y:auto;">
            <div style="text-align:center;padding:30px;color:var(--text-dim);font-size:13px;">Nenhum parceiro cadastrado.</div>
        </div>
    </div>
</div>

<div style="margin-top:16px;padding:12px 16px;border-radius:var(--radius-md);background:rgba(37,211,102,0.06);border:1px solid rgba(37,211,102,0.15);font-size:12px;color:var(--text-dim);display:flex;align-items:center;gap:10px;">
    <i class="ph-bold ph-info" style="color:#25D366;font-size:18px;"></i>
    <span>Modo <strong id="waStatusBadge" style="color:#25D366;">wa.link</strong> — os botoes abrem o WhatsApp Web com a mensagem preenchida. Para envio automatico, configure a <a href="https://developers.facebook.com/docs/whatsapp/cloud-api" target="_blank" style="color:#25D366;">Cloud API</a> em <code style="background:rgba(0,0,0,0.3);padding:1px 5px;border-radius:3px;">whatsapp_config.php</code>.</span>
</div>

<script>
function esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

var waMode = 'clientes';
var segRecords = [];
var waContatoAtual = { nome: '', telefone: '', segmento: '', cidade: '', bairro: '', precisa9: false };

function fmtTelBr(s) {
    if (!s) return '';
    var d = s.replace(/\D/g, '');
    if (d.length === 11) return '(' + d.slice(0,2) + ') ' + d.slice(2,7) + '-' + d.slice(7);
    if (d.length === 10) return '(' + d.slice(0,2) + ') ' + d.slice(2,6) + '-' + d.slice(6);
    if (d.length >= 8) return d.slice(0,4) + '-' + d.slice(4);
    return s;
}

function waSetMode(mode) {
    waMode = mode;
    document.getElementById('waModeClientes').className = 'wa-mode-btn' + (mode === 'clientes' ? ' active' : '');
    document.getElementById('waModeSegmentos').className = 'wa-mode-btn' + (mode === 'segmentos' ? ' active' : '');
    document.getElementById('waRowCliente').style.display = mode === 'clientes' ? 'flex' : 'none';
    document.getElementById('waRowSegmento').style.display = mode === 'segmentos' ? 'flex' : 'none';
    if (mode === 'clientes') waListarClientes();
    else waListarSegmento();
}

async function waListarSegmento() {
    var seg = document.getElementById('waSegmento').value;
    if (!seg) { document.getElementById('waClientesLista').innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-dim);font-size:13px;">Selecione um segmento acima.</div>'; return; }
    var q = document.getElementById('waBuscaSeg').value.trim();

    document.getElementById('waClientesLista').innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-dim);font-size:13px;">Carregando...</div>';
    var d = await fetch('api/whatsapp.php?action=segment_list&seg=' + encodeURIComponent(seg) + (q ? '&q=' + encodeURIComponent(q) : '')).then(function(r){return r.json()});
    segRecords = d.records || [];

    document.getElementById('waSegCount').textContent = segRecords.length + ' contatos';

    // Preenche o select de cliente rapido com os registros do segmento
    var sel = document.getElementById('waCliente');
    sel.innerHTML = '<option value="">Selecione um contato...</option>';
    var segLabel = document.getElementById('waSegmento').options[document.getElementById('waSegmento').selectedIndex]?.text || '';

    segRecords.forEach(function(r) {
        sel.innerHTML += '<option value="' + r.cnpj + '" data-tel="' + r.telefone + '" data-nome="' + r.nome.replace(/'/g,"&#39;") + '">' + r.nome + '</option>';
    });

    // Renderiza lista
    var html = '';
    segRecords.forEach(function(r) {
        var cid = esc(r.cidade || '');
        var bai = esc(r.bairro || '');
        var link9 = r.wa_link_9 ? ' <a href="' + esc(r.wa_link_9) + '" target="_blank" class="wa-cliente-btn wa-cliente-btn-9" onclick="event.stopPropagation()" title="Tentar com 9\u00ba digito"><i class="ph-bold ph-plus-circle"></i></a>' : '';
        var precisa9 = r.precisa_9 ? '<span style="margin-left:6px;font-size:9px;color:#f39c12;font-weight:600;">\u26A0\uFE0F 9?</span>' : '';
        var p9 = r.precisa_9 ? 'true' : 'false';
        var nomeEsc = esc(r.nome);
        var nomeAttr = esc(r.nome).replace(/'/g,"&#39;");
        html += '<div onclick="waSelecionarCliente(\'' + esc(r.cnpj) + '\',\'' + esc(r.telefone) + '\',\'' + nomeAttr + '\',\'' + esc(segLabel) + '\',\'' + cid + '\',\'' + bai + '\',' + p9 + ')" class="wa-cliente-item" data-tel="' + esc(r.telefone) + '">' +
            '<div class="wa-cliente-info"><strong>' + nomeEsc + '</strong><br>' +
            '<span>' + esc(r.telefone_formatado) + precisa9 + '</span>' +
            (r.bairro ? '<span style="margin-left:8px;font-size:10px;color:var(--text-dim);">' + esc(r.bairro) + (r.cidade ? ' - ' + esc(r.cidade) : '') + '</span>' : '') +
            '</div>' +
            '<div style="display:flex;gap:4px;">' +
            '<a href="' + esc(r.wa_link) + '" target="_blank" class="wa-cliente-btn" onclick="event.stopPropagation()"><i class="ph-bold ph-whatsapp-logo"></i></a>' +
            link9 +
            '</div>' +
            '</div>';
    });
    document.getElementById('waClientesLista').innerHTML = html || '<div style="text-align:center;padding:20px;color:var(--text-dim);font-size:13px;">Nenhum contato com WhatsApp neste segmento.</div>';
}

async function waCarregar() {
    var s = await fetch('api/whatsapp.php?action=status').then(function(r){return r.json()});
    var badge = document.getElementById('waStatus');
    badge.textContent = s.mode === 'api' ? 'API Ativa' : 'wa.link';
    badge.style.color = s.mode === 'api' ? '#2ecc71' : '#f39c12';
    document.getElementById('waStatusBadge').textContent = badge.textContent;

    waListarClientes();
}

async function waListarClientes() {
    var q = (document.getElementById('waBusca').value || '').trim();
    var url = 'api/clientes.php?action=list&limit=500';
    if (q) url += '&q=' + encodeURIComponent(q);

    var d = await fetch(url).then(function(r){return r.json()});
    var records = d.records || [];

    document.getElementById('waClientes').textContent = records.length;

    var comTel = records.filter(function(c){ return c.telefone && c.telefone.replace(/\D/g,'').length >= 10; });
    document.getElementById('waComTel').textContent = comTel.length;

    var sel = document.getElementById('waCliente');
    var curVal = sel.value;
    sel.innerHTML = '<option value="">Selecione um parceiro...</option>';
    var html = '';

    comTel.forEach(function(c) {
        var tel = c.telefone.replace(/\D/g, '');
        var nome = c.razao_social || c.nome_fantasia || 'Parceiro';
        sel.innerHTML += '<option value="' + c.id + '" data-tel="' + tel + '" data-nome="' + nome.replace(/'/g,"&#39;") + '">' + nome + '</option>';

        var prec9 = (tel.length === 10 && tel.substring(2).length === 8 && /^[6-9]/.test(tel.substring(2))) ? 'true' : 'false';
        html += '<div onclick="waSelecionarCliente(' + c.id + ',\'' + tel + '\',\'' + nome.replace(/'/g,"\\'") + '\',\'\',\'\',\'\',' + prec9 + ')" class="wa-cliente-item" data-tel="' + tel + '">' +
            '<div class="wa-cliente-info"><strong>' + nome + '</strong><br><span>' + fmtTelBr(c.telefone) + '</span></div>' +
            '<div style="display:flex;gap:4px;">' +
            '<a href="https://wa.me/55' + tel + '" target="_blank" class="wa-cliente-btn" onclick="event.stopPropagation()"><i class="ph-bold ph-whatsapp-logo"></i></a>' +
            '<button onclick="event.stopPropagation();waExcluirCliente(' + c.id + ')" class="wa-cliente-btn wa-cliente-btn-del" title="Excluir parceiro"><i class="ph-bold ph-trash"></i></button>' +
            '</div>' +
            '</div>';
    });

    document.getElementById('waClientesLista').innerHTML = html || '<div style="text-align:center;padding:20px;color:var(--text-dim);font-size:13px;">Nenhum parceiro com WhatsApp cadastrado.</div>';

    if (curVal) sel.value = curVal;
}

function waAtualizarNumero() {
    var tel = waContatoAtual.telefone;
    var add9 = document.getElementById('waToggle9').checked;
    if (add9 && tel.length === 10 && tel.substring(2).length === 8 && /^[6-9]/.test(tel.substring(2))) {
        tel = tel.substring(0, 2) + '9' + tel.substring(2);
    }
    document.getElementById('waNumero').value = fmtTelBr(tel);
}

function waSelecionarCliente(id, tel, nome, segmento, cidade, bairro, precisa9) {
    waContatoAtual = { nome: nome, telefone: tel, segmento: segmento || '', cidade: cidade || '', bairro: bairro || '', precisa9: !!precisa9 };
    var chk = document.getElementById('waToggle9');
    if (chk) chk.checked = precisa9;
    document.getElementById('waCliente').value = id;
    waAtualizarNumero();
    document.getElementById('waMensagem').value = 'Ol\u00e1 ' + nome + '! Tudo bem?';
    document.getElementById('waResult').style.display = 'none';

    // Destaca na lista
    document.querySelectorAll('.wa-cliente-item').forEach(function(el){ el.classList.remove('selected'); });
    document.querySelectorAll('.wa-cliente-item').forEach(function(el){
        if (el.getAttribute('data-tel') === tel) el.classList.add('selected');
    });
}

function waAbrirCliente() {
    var opt = document.getElementById('waCliente').options[document.getElementById('waCliente').selectedIndex];
    if (!opt || !opt.value) { alert('Selecione um cliente.'); return; }
    waSelecionarCliente(opt.value, opt.getAttribute('data-tel'), opt.getAttribute('data-nome'));
    waEnviar();
}

function waPreencherMsg() {
    var tpl = document.getElementById('waTemplate').value;
    if (!tpl) return;
    var opt = document.getElementById('waCliente').options[document.getElementById('waCliente').selectedIndex];
    var nome = opt?.getAttribute('data-nome') || 'Cliente';
    var msgs = {
        boas_vindas: 'Ol\u00e1 ' + nome + '! Aqui \u00e9 da LC Solu\u00e7\u00f5es em M\u00f3veis. Recebemos seu contato e estamos prontos para transformar seu ambiente com m\u00f3veis planejados. Como podemos ajudar?',
        orcamento: 'Ol\u00e1 ' + nome + ', tudo bem? Vi que voc\u00ea solicitou um or\u00e7amento conosco. Podemos agendar uma visita t\u00e9cnica para conhecer melhor seu espa\u00e7o?',
        acompanhamento: 'Ol\u00e1 ' + nome + ', passando pra saber como est\u00e1 o andamento do seu projeto conosco. Precisa de alguma ajuda?',
        finalizacao: 'Ol\u00e1 ' + nome + '! Seu projeto ficou incr\u00edvel! Ficamos muito felizes em transformar seu espa\u00e7o. Se precisar de algo mais, estamos \u00e0 disposi\u00e7\u00e3o.'
    };
    document.getElementById('waMensagem').value = msgs[tpl] || '';
}

function waGerarIA() {
    var contato = waContatoAtual;
    if (!contato.nome) { alert('Selecione um contato primeiro.'); return; }

    var btn = document.getElementById('waBtnIA');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ph-bold ph-spinner"></i> Gerando...'; }

    var msgEl = document.getElementById('waMensagem');
    msgEl.value = 'Gerando mensagem personalizada...';

    fetch('api/chat.php?action=gerar_mensagem', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            nome: contato.nome,
            segmento: contato.segmento,
            cidade: contato.cidade,
            bairro: contato.bairro,
            tipo: 'parceria'
        })
    }).then(function(r){return r.json()}).then(function(d){
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-magic-wand"></i> Gerar com IA'; }
        if (d.response) {
            msgEl.value = d.response;
        } else {
            msgEl.value = 'Erro ao gerar mensagem.';
        }
    }).catch(function(){
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-magic-wand"></i> Gerar com IA'; }
        msgEl.value = 'Erro de conexao com o servidor.';
    });
}

function waExcluirCliente(id) {
    if (!confirm('Tem certeza que deseja excluir este parceiro?')) return;
    fetch('api/clientes.php?action=delete&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (d.success) waListarClientes();
        else alert(d.error || 'Erro ao excluir');
    });
}

function waEnviar() {
    var tel = document.getElementById('waNumero').value.replace(/\D/g, '');
    var msg = document.getElementById('waMensagem').value.trim();
    if (!tel) { alert('Selecione um cliente com telefone.'); return; }
    if (!msg) { alert('Digite uma mensagem.'); return; }

    var r = document.getElementById('waResult');
    r.style.display = 'block';
    r.style.background = 'rgba(37,211,102,0.1)';
    r.style.color = '#25D366';
    r.innerHTML = '<i class="ph-bold ph-whatsapp-logo"></i> Abrindo WhatsApp...';

    window.open('https://wa.me/55' + tel + '?text=' + encodeURIComponent(msg), '_blank');

    setTimeout(function() {
        r.innerHTML = '<i class="ph-bold ph-check-circle"></i> WhatsApp aberto! Envie a mensagem clicando no envio.';
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tab-whatsapp')) waCarregar();
});
</script>

<style>
.wa-mode-btn {
    background:transparent;border:none;color:var(--text-dim);font-size:12px;
    font-weight:600;padding:6px 14px;border-radius:5px;cursor:pointer;transition:all .2s;
    font-family:var(--font-body);
}
.wa-mode-btn.active { background:var(--gold);color:#000; }
.wa-mode-btn:hover:not(.active) { color:var(--text); }

.wa-cliente-item {
    display:flex;justify-content:space-between;align-items:center;
    padding:8px 10px;border-radius:var(--radius-sm);cursor:pointer;
    transition:background .2s;border-bottom:1px solid var(--border);
}
.wa-cliente-item:hover { background:rgba(37,211,102,0.08); }
.wa-cliente-item.selected { background:rgba(37,211,102,0.12); border-left:3px solid #25D366; }
.wa-cliente-info strong { font-size:13px;color:var(--text); }
.wa-cliente-info span { font-size:11px;color:var(--text-dim); }
.wa-cliente-btn {
    background:#25D366;color:#000;border:none;border-radius:50%;
    width:32px;height:32px;display:flex;align-items:center;justify-content:center;
    text-decoration:none;flex-shrink:0;
}
.wa-cliente-btn:hover { background:#1da851; }
.wa-cliente-btn-9 { background:rgba(243,156,18,0.2)!important;color:#f39c12!important;font-size:14px; }
.wa-cliente-btn-9:hover { background:rgba(243,156,18,0.4)!important; }
.wa-cliente-btn-del { background:rgba(231,76,60,0.15)!important;color:#e74c3c!important;font-size:14px!important; }
.wa-cliente-btn-del:hover { background:rgba(231,76,60,0.3)!important; }
</style>

</section>
