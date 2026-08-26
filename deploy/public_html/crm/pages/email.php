<section id="tab-email">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Email Marketing
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Dispare campanhas para seus parceiros
        </span>
    </h1>
</div>

<?php
$emailConfig = include __DIR__ . '/../email_config.php';
?>

<div class="exp-controls">
    <div class="row" style="margin-bottom:8px;">
        <span style="font-size:11px;color:var(--gold);font-weight:600;letter-spacing:0.5px;text-transform:uppercase;cursor:pointer;" onclick="emailToggleSmtpConfig()">Configuracao SMTP</span>
        <span id="emailSmtpStatus" style="font-size:11px;padding:2px 8px;border-radius:4px;margin-left:8px;"></span>
    </div>

    <div id="emailSmtpConfig" style="display:none;margin-bottom:14px;padding:16px;background:rgba(0,0,0,0.4);border-radius:var(--radius-md);border:1px solid var(--border);">
        <div class="row">
            <label>Provedor</label>
            <select id="smtpProvider" onchange="smtpProviderChanged()" style="min-width:180px;">
                <option value="brevo">Brevo (Sendinblue)</option>
                <option value="sendgrid">SendGrid</option>
                <option value="gmail">Gmail SMTP</option>
                <option value="amazon_ses">Amazon SES</option>
                <option value="mailgun">Mailgun</option>
                <option value="outros">Outros</option>
            </select>
        </div>
        <div class="row">
            <label>SMTP Host</label>
            <input type="text" id="smtpHost" placeholder="smtp-relay.brevo.com" style="flex:1;">
            <label style="min-width:auto;">Porta</label>
            <input type="number" id="smtpPort" value="587" style="width:70px;">
            <label style="min-width:auto;"><input type="checkbox" id="smtpTls" checked> TLS</label>
        </div>
        <div class="row">
            <label>Usuario</label>
            <input type="text" id="smtpUser" placeholder="Login SMTP" style="flex:1;">
            <label style="min-width:auto;">Senha</label>
            <input type="password" id="smtpPass" placeholder="Senha SMTP" style="flex:1;">
        </div>
        <div class="row">
            <label>Remetente</label>
            <input type="text" id="smtpFromName" placeholder="LC Solucoes em Moveis" style="flex:1;">
            <input type="email" id="smtpFromEmail" placeholder="email@seudominio.com" style="flex:1;">
        </div>
        <div class="row">
            <label>Limites</label>
            <input type="number" id="smtpMaxMin" value="30" style="width:70px;" title="Max por minuto">
            <span style="font-size:11px;color:#888;">/min</span>
            <input type="number" id="smtpMaxDay" value="300" style="width:80px;margin-left:8px;" title="Max por dia">
            <span style="font-size:11px;color:#888;">/dia</span>
        </div>
        <div class="row">
            <button class="btn-primary" onclick="smtpTestar()" style="background:#2ecc71;color:#000;">Testar Conexao</button>
            <button class="btn-primary" onclick="smtpSalvar()">Salvar Configuracao</button>
            <span id="smtpMsg" style="font-size:12px;margin-left:8px;"></span>
        </div>
    </div>

    <div class="row">
        <label>Segmentos</label>
        <div class="exp-seg-grid" id="emailSegGrid">
            <?php foreach ($segments as $key => $s): ?>
            <label><input type="checkbox" value="<?= $key ?>" checked onchange="emailUpdateStatus()"><span><?= $s['label'] ?></span></label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="row">
        <label>Filtro</label>
        <select id="emailStatus">
            <option value="todos">Todos</option>
            <option value="ativos">Ativos</option>
            <option value="com_telefone">Com telefone</option>
            <option value="sem_telefone">Sem telefone</option>
            <option value="ativos_tel">Ativos + Telefone</option>
        </select>
        <input type="text" id="emailSearch" placeholder="Buscar por nome, bairro..." onkeydown="if(event.key==='Enter') emailCarregar()">
        <button class="btn-primary" onclick="emailCarregar()">Carregar Contatos</button>
        <span id="emailServerStatus" class="badge" style="display:inline-flex;font-size:11px;padding:4px 10px;border-radius:4px;"></span>
    </div>
    <div class="row">
        <label>Cidade</label>
        <div class="city-filter">
            <select id="emailCidade" placeholder="Filtrar por cidade..." autocomplete="off">
                <option value="">Todas as cidades</option>
                <?php foreach ($cidades as $cod => $nome): ?>
                <option value="<?= $cod ?>"><?= htmlspecialchars($nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="exp-table-wrap">
    <div class="exp-toolbar" id="emailToolbar" style="display:none;">
        <div class="left">
            <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;">
                <input type="checkbox" id="emailSelectAll" onchange="emailToggleAll()"> Selecionar todos
            </label>
            <span class="count" id="emailCount"></span>
        </div>
        <div>
            <span style="font-size:12px;color:#888;">Registros com email: <strong id="emailComEmail">0</strong></span>
        </div>
    </div>
    <div id="emailTableContainer">
        <div class="exp-empty" id="emailEmpty">
            <h3>Carregue os contatos para enviar emails</h3>
            <p>Selecione os segmentos e clique em "Carregar Contatos". Depois escolha um template e envie.</p>
        </div>
    </div>
</div>

<div class="exp-controls" id="emailCompose" style="display:none;margin-top:16px;">
    <h3 style="font-family:var(--font-heading);font-size:16px;color:var(--gold);margin:0 0 16px;font-weight:700;">Compor Disparo de Email</h3>
    <div class="row">
        <label>Template</label>
        <select id="emailTemplate" style="min-width:250px;" onchange="emailPreviewTemplate()">
            <option value="">Selecione um template...</option>
        </select>
        <label style="min-width:auto;">Assunto</label>
        <input type="text" id="emailSubject" placeholder="Assunto do email..." style="flex:1;">
    </div>
    <div id="emailTemplateVars" style="display:none;">
        <div class="row">
            <label>URL da Logo</label>
            <input type="text" id="evLogoUrl" value="https://lcmoveis.com.br/assets/img/logo.jpg" style="flex:1;" placeholder="URL da logo LC">
        </div>
        <div class="row">
            <label>Link Contato</label>
            <input type="text" id="evLinkContato" value="https://w.app/lcsolucoesemmoveis" style="flex:1;">
        </div>
        <div class="row">
            <label>Link Site</label>
            <input type="text" id="evLinkSite" value="https://lcmoveis.com.br" style="flex:1;">
        </div>
        <div id="evNovidade" class="row" style="display:none;">
            <label>Titulo Novidade</label>
            <input type="text" id="evTituloNovidade" value="Novidades LC Moveis" style="flex:1;">
            <label>Descricao</label>
            <input type="text" id="evDescNovidade" value="Confira as novas linhas de moveis planejados 2026!" style="flex:1;">
        </div>
        <div id="evPromocao" class="row" style="display:none;">
            <label>Desconto</label>
            <input type="text" id="evDesconto" value="20%" style="width:80px;">
            <label>Produto</label>
            <input type="text" id="evProduto" value="Moveis Planejados" style="flex:1;">
            <label>Cupom</label>
            <input type="text" id="evCupom" value="LC20" style="width:120px;">
        </div>
        <div id="evOferta" class="row" style="display:none;">
            <label>Titulo Oferta</label>
            <input type="text" id="evTituloOferta" value="Oferta especial para parceiros" style="flex:1;">
            <label>Descricao</label>
            <input type="text" id="evDescOferta" value="Condicoes exclusivas validas por tempo limitado." style="flex:1;">
        </div>
        <div id="evParceiro" class="row" style="display:none;">
            <label>Mensagem Adicional</label>
            <textarea id="evMsgAdicional" rows="2" style="flex:1;padding:7px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:13px;outline:none;resize:vertical;" placeholder="Mensagem personalizada para o parceiro..."></textarea>
        </div>
    </div>
    <div id="emailPreview" style="display:none;margin:10px 0;background:#0A0A0A;border:1px solid rgba(197,162,83,0.25);border-radius:12px;max-height:400px;overflow-y:auto;padding:12px;">
        <div style="font-size:11px;color:#C5A253;margin-bottom:6px;letter-spacing:1px;">Pre-visualizacao do Template</div>
        <iframe id="emailPreviewFrame" style="width:100%;height:320px;border:none;background:#0A0A0A;" sandbox="allow-same-origin"></iframe>
    </div>
    <div class="row">
        <button class="btn-primary" onclick="emailEnviar()" id="emailSendBtn" style="padding:12px 32px;font-size:14px;">
            Enviar para <span id="emailSendCount">0</span> contatos
        </button>
        <button class="btn-primary" onclick="emailEnviarTeste()" style="background:linear-gradient(135deg,#27ae60,#1e8449) !important;color:#000 !important;padding:12px 24px;font-size:13px;">
            Enviar Teste
        </button>
        <span id="emailSendStatus" style="font-size:13px;margin-left:10px;"></span>
    </div>
</div>

<script>
let emailAllData = [];
let emailSelected = new Set();
let emailTotal = 0;
let emailCitySelect = null;

document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('emailCidade');
    if (el) emailCitySelect = new TomSelect(el, {
        maxItems: 1,
        placeholder: 'Filtrar por cidade...',
        allowEmptyOption: true
    });
});

function emailUpdateStatus() {
    document.getElementById('emailToolbar').style.display = 'none';
    document.getElementById('emailTableContainer').innerHTML = '<div class="exp-empty"><h3>Segmentos alterados. Clique em "Carregar Contatos".</h3></div>';
    document.getElementById('emailCompose').style.display = 'none';
}

function emailGetSegments() {
    const checks = document.querySelectorAll('#emailSegGrid input:checked');
    return Array.from(checks).map(function(c) { return c.value; });
}

function emailCarregar() {
    const segs = emailGetSegments();
    if (!segs.length) { alert('Selecione pelo menos um segmento.'); return; }
    const status = document.getElementById('emailStatus').value;
    const q = document.getElementById('emailSearch').value.trim();
    let url = 'api/export_data.php?segments[]=' + segs.join('&segments[]=') + '&status=' + status + '&q=' + encodeURIComponent(q) + '&limit=5000';
    const cidade = emailCitySelect ? emailCitySelect.getValue() : '';
    if (cidade) url += '&cidade=' + encodeURIComponent(cidade);
    document.getElementById('emailTableContainer').innerHTML = '<div class="exp-empty"><h3>Carregando contatos...</h3></div>';
    document.getElementById('emailToolbar').style.display = 'none';
    document.getElementById('emailCompose').style.display = 'none';
    fetch(url).then(function(r) { return r.json(); }).then(function(data) {
        emailAllData = data.records;
        emailTotal = data.total;
        emailSelected = new Set();
        emailRenderTable();
        emailLoadTemplates();
    }).catch(function() {
        document.getElementById('emailTableContainer').innerHTML = '<div class="exp-empty"><h3>Erro ao carregar dados</h3></div>';
    });
}

function emailRenderTable() {
    const container = document.getElementById('emailTableContainer');
    const toolbar = document.getElementById('emailToolbar');
    if (!emailAllData.length) {
        container.innerHTML = '<div class="exp-empty"><h3>Nenhum registro encontrado</h3></div>';
        toolbar.style.display = 'none';
        return;
    }
    toolbar.style.display = 'flex';
    var comEmail = emailAllData.filter(function(r) { return r.correio_eletronico && r.correio_eletronico.includes('@'); });
    document.getElementById('emailComEmail').textContent = comEmail.length;
    document.getElementById('emailCount').textContent = emailTotal + ' registros (' + emailAllData.length + ' carregados)';
    document.getElementById('emailSelectAll').checked = emailSelected.size === emailAllData.length && emailAllData.length > 0;
    var segColors = <?= json_encode($segColors) ?>;
    var html = '<div style="overflow-x:auto;"><table><thead><tr>' +
        '<th class="exp-check-col"></th><th>Segmento</th><th>Razao Social</th><th>Fantasia</th><th>Bairro</th><th>Cidade</th><th>Telefone</th><th>Email</th><th>Status</th>' +
        '</tr></thead><tbody>';
    emailAllData.forEach(function(row, idx) {
        var segColor = segColors[row.segment] || '#888';
        var checked = emailSelected.has(idx) ? 'checked' : '';
        var tel = (row.ddd && row.telefone) ? '(' + row.ddd + ') ' + row.telefone : '-';
        var email = row.correio_eletronico || '-';
        var hasEmail = row.correio_eletronico && row.correio_eletronico.includes('@');
        var sit = row.situacao_cadastral === '02' ? '<span class="badge badge-ativo">Ativa</span>' :
                 row.situacao_cadastral === '08' ? '<span class="badge badge-baixada">Baixada</span>' :
                 '<span class="badge badge-outro">' + (row.situacao_cadastral || 'N/I') + '</span>';
        html += '<tr>' +
            '<td class="exp-check-col"><input type="checkbox" ' + checked + ' onchange="emailToggle(' + idx + ')" ' + (!hasEmail ? 'disabled title="Sem email"' : '') + '></td>' +
            '<td><span class="exp-seg-badge" style="background:' + segColor + '">' + (row.segment_label || row.segment) + '</span></td>' +
            '<td><strong>' + (row.razao_social || row.nome_fantasia || 'N/I') + '</strong></td>' +
            '<td>' + (row.nome_fantasia || '-') + '</td>' +
            '<td>' + (row.bairro || '-') + '</td>' +
            '<td>' + (row.nome_municipio || '-') + '</td>' +
            '<td>' + tel + '</td>' +
            '<td style="font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;">' + (hasEmail ? email : '<span style="color:#ccc;">sem email</span>') + '</td>' +
            '<td>' + sit + '</td></tr>';
    });
    html += '</tbody></table></div>';
    container.innerHTML = html;
    document.getElementById('emailCompose').style.display = 'block';
    emailUpdateSendCount();
}

function emailToggle(idx) {
    if (emailSelected.has(idx)) emailSelected.delete(idx);
    else emailSelected.add(idx);
    document.getElementById('emailSelectAll').checked = emailSelected.size === emailAllData.length;
    emailUpdateSendCount();
}

function emailToggleAll() {
    var checked = document.getElementById('emailSelectAll').checked;
    emailSelected.clear();
    if (checked) {
        emailAllData.forEach(function(row, idx) {
            if (row.correio_eletronico && row.correio_eletronico.includes('@')) emailSelected.add(idx);
        });
    }
    emailRenderTable();
    emailUpdateSendCount();
}

function emailUpdateSendCount() {
    document.getElementById('emailSendCount').textContent = emailSelected.size;
}

var smtpConfigLoaded = false;

function emailToggleSmtpConfig() {
    var panel = document.getElementById('emailSmtpConfig');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    if (!smtpConfigLoaded) smtpCarregarConfig();
}

function smtpProviderChanged() {
    var providers = {
        brevo: {host:'smtp-relay.brevo.com',port:587},
        sendgrid: {host:'smtp.sendgrid.net',port:587},
        gmail: {host:'smtp.gmail.com',port:587},
        amazon_ses: {host:'email-smtp.us-east-1.amazonaws.com',port:587},
        mailgun: {host:'smtp.mailgun.org',port:587},
    };
    var p = providers[document.getElementById('smtpProvider').value];
    if (p) {
        document.getElementById('smtpHost').value = p.host;
        document.getElementById('smtpPort').value = p.port;
    }
}

function smtpCarregarConfig() {
    setTimeout(function() {
        fetch('email_config.php?action=get_config').then(function(r){return r.json()}).then(function(data) {
            if (data.config) {
                var c = data.config;
                document.getElementById('smtpProvider').value = c.provider || 'brevo';
                document.getElementById('smtpHost').value = c.host || '';
                document.getElementById('smtpPort').value = c.port || 587;
                document.getElementById('smtpUser').value = c.username || '';
                document.getElementById('smtpFromName').value = c.from_name || '';
                document.getElementById('smtpFromEmail').value = c.from_email || '';
                document.getElementById('smtpMaxMin').value = c.max_per_minute || 30;
                document.getElementById('smtpMaxDay').value = c.max_per_day || 300;
                document.getElementById('smtpTls').checked = c.use_tls !== false;
                document.getElementById('smtpPass').value = '';
                smtpConfigLoaded = true;
                if (c.host) document.getElementById('emailSmtpStatus').innerHTML = '<span style="color:#2ecc71;"> Configurado: ' + c.host + '</span>';
                else document.getElementById('emailSmtpStatus').innerHTML = '<span style="color:#f39c12;"> Nao configurado</span>';
            }
        }).catch(function() {
            document.getElementById('emailSmtpStatus').innerHTML = '<span style="color:#e74c3c;"> Servidor offline</span>';
        });
    }, 500);
}

function smtpTestar() {
    var msg = document.getElementById('smtpMsg');
    msg.innerHTML = 'Testando...';
    var data = {
        host: document.getElementById('smtpHost').value,
        port: parseInt(document.getElementById('smtpPort').value) || 587,
        username: document.getElementById('smtpUser').value,
        password: document.getElementById('smtpPass').value,
        use_tls: document.getElementById('smtpTls').checked,
    };
    fetch('email_config.php?action=test_config', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r){return r.json()}).then(function(res) {
        if (res.success) msg.innerHTML = '<span style="color:#2ecc71;"> ' + res.message + '</span>';
        else msg.innerHTML = '<span style="color:#e74c3c;"> ' + (res.error || 'Falha') + '</span>';
    }).catch(function() {
        msg.innerHTML = '<span style="color:#e74c3c;"> Erro de conexao</span>';
    });
}

function smtpSalvar() {
    var msg = document.getElementById('smtpMsg');
    msg.innerHTML = 'Salvando...';
    var data = {
        provider: document.getElementById('smtpProvider').value,
        host: document.getElementById('smtpHost').value,
        port: parseInt(document.getElementById('smtpPort').value) || 587,
        username: document.getElementById('smtpUser').value,
        password: document.getElementById('smtpPass').value,
        from_name: document.getElementById('smtpFromName').value,
        from_email: document.getElementById('smtpFromEmail').value,
        use_tls: document.getElementById('smtpTls').checked,
        max_per_minute: parseInt(document.getElementById('smtpMaxMin').value) || 30,
        max_per_day: parseInt(document.getElementById('smtpMaxDay').value) || 300,
    };
    fetch('email_config.php?action=save_config', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r){return r.json()}).then(function(res) {
        if (res.success) {
            msg.innerHTML = '<span style="color:#2ecc71;"> Configuracao salva!</span>';
            smtpCarregarConfig();
        } else msg.innerHTML = '<span style="color:#e74c3c;"> ' + (res.error || 'Erro') + '</span>';
    }).catch(function() {
        msg.innerHTML = '<span style="color:#e74c3c;"> Erro de conexao</span>';
    });
}

function emailLoadTemplates() {
    var sel = document.getElementById('emailTemplate');
    sel.innerHTML = '<option value="">Carregando templates...</option>';
    fetch('api/email_api.php?action=templates').then(function(r) { return r.json(); }).then(function(data) {
        if (data.templates) {
            sel.innerHTML = '<option value="">Selecione um template...</option>';
            data.templates.forEach(function(t) {
                sel.innerHTML += '<option value="' + t.file + '">' + t.label + '</option>';
            });
        }
        document.getElementById('emailServerStatus').textContent = 'Servidor online';
        document.getElementById('emailServerStatus').style.background = '#d4edda';
        document.getElementById('emailServerStatus').style.color = '#155724';
    }).catch(function() {
        sel.innerHTML = '<option value="">Servidor offline</option>';
        document.getElementById('emailServerStatus').textContent = 'Offline';
        document.getElementById('emailServerStatus').style.background = '#f8d7da';
        document.getElementById('emailServerStatus').style.color = '#721c24';
    });
}

function emailPreviewTemplate() {
    var tpl = document.getElementById('emailTemplate').value;
    var varsDiv = document.getElementById('emailTemplateVars');
    var preview = document.getElementById('emailPreview');
    document.getElementById('evNovidade').style.display = 'none';
    document.getElementById('evPromocao').style.display = 'none';
    document.getElementById('evOferta').style.display = 'none';
    document.getElementById('evParceiro').style.display = 'none';
    if (!tpl) { varsDiv.style.display = 'none'; preview.style.display = 'none'; return; }
    varsDiv.style.display = 'block';
    if (tpl === 'novidades.html') document.getElementById('evNovidade').style.display = 'flex';
    else if (tpl === 'promocoes.html') document.getElementById('evPromocao').style.display = 'flex';
    else if (tpl === 'ofertas.html') document.getElementById('evOferta').style.display = 'flex';
    else if (tpl === 'parceiros.html') document.getElementById('evParceiro').style.display = 'flex';
    preview.style.display = 'block';
    var nome = emailSelected.size > 0 ? (emailAllData[[...emailSelected][0]]?.razao_social || 'Parceiro') : 'Parceiro';
    var date = new Date().toLocaleDateString('pt-BR');
    var frame = document.getElementById('emailPreviewFrame');
    fetch('api/email_api.php?action=get&file=' + encodeURIComponent(tpl)).then(function(r) { return r.text(); }).then(function(html) {
        html = html.replace(/\{\{NOME\}\}/g, nome)
                   .replace(/\{\{DATA\}\}/g, date)
                   .replace(/\{\{LOGO_URL\}\}/g, document.getElementById('evLogoUrl')?.value || 'https://lcsolucoesemmoveis.com.br/assets/img/logo.jpg')
                   .replace(/\{\{LINK_CONTATO\}\}/g, document.getElementById('evLinkContato').value)
                   .replace(/\{\{LINK_SITE\}\}/g, document.getElementById('evLinkSite').value)
                   .replace(/\{\{TITULO_NOVIDADE\}\}/g, document.getElementById('evTituloNovidade')?.value || '')
                   .replace(/\{\{DESCRICAO_NOVIDADE\}\}/g, document.getElementById('evDescNovidade')?.value || '')
                   .replace(/\{\{DESCONTO\}\}/g, document.getElementById('evDesconto')?.value || '')
                   .replace(/\{\{PRODUTO\}\}/g, document.getElementById('evProduto')?.value || '')
                   .replace(/\{\{CUPOM\}\}/g, document.getElementById('evCupom')?.value || '')
                   .replace(/\{\{TITULO_OFERTA\}\}/g, document.getElementById('evTituloOferta')?.value || '')
                   .replace(/\{\{DESCRICAO_OFERTA\}\}/g, document.getElementById('evDescOferta')?.value || '')
                   .replace(/\{\{MENSAGEM_ADICIONAL\}\}/g, document.getElementById('evMsgAdicional')?.value || '')
                   .replace(/\{\{DATA_INICIO\}\}/g, date)
                   .replace(/\{\{DATA_FIM\}\}/g, date)
                   .replace(/\{\{PRECO_NORMAL\}\}/g, '0,00')
                   .replace(/\{\{PRECO_PARCEIRO\}\}/g, '0,00');
        frame.srcdoc = html;
    }).catch(function() {
        frame.srcdoc = '<div style="padding:20px;text-align:center;color:#888;">Erro ao carregar template</div>';
    });
}

function emailGetSelectedData() {
    if (emailSelected.size === 0) { alert('Nenhum contato selecionado.'); return null; }
    return Array.from(emailSelected).map(function(idx) { return emailAllData[idx]; });
}

function emailEnviar() {
    var data = emailGetSelectedData();
    if (!data) return;
    var tpl = document.getElementById('emailTemplate').value;
    var subject = document.getElementById('emailSubject').value.trim();
    if (!tpl) { alert('Selecione um template.'); return; }
    if (!subject) { alert('Digite o assunto do email.'); return; }
    if (!confirm('Enviar email para ' + data.length + ' contatos? Template: ' + tpl + ' Assunto: ' + subject)) return;
    emailDoSend(data, tpl, subject);
}

function emailEnviarTeste() {
    var tpl = document.getElementById('emailTemplate').value;
    var subject = document.getElementById('emailSubject').value.trim();
    if (!tpl) { alert('Selecione um template.'); return; }
    if (!subject) { alert('Digite o assunto do email.'); return; }
    var emailTeste = prompt('Digite o email de teste:', '<?= $emailConfig['from_email'] ?>');
    if (!emailTeste) return;
    var data = [{ correio_eletronico: emailTeste, razao_social: 'Teste LC CRM', segment: 'arquitetos_ms', segment_label: 'Teste' }];
    emailDoSend(data, tpl, subject);
}

function emailDoSend(data, tpl, subject) {
    var btn = document.getElementById('emailSendBtn');
    var status = document.getElementById('emailSendStatus');
    btn.disabled = true;
    status.innerHTML = 'Enviando...';
    var recipients = data.filter(function(r) { return r.correio_eletronico && r.correio_eletronico.includes('@'); }).map(function(r) {
        return { email: r.correio_eletronico, nome: r.razao_social || r.nome_fantasia || 'Parceiro', vars: {} };
    });
    if (!recipients.length) { alert('Nenhum contato com email valido selecionado.'); btn.disabled = false; status.innerHTML = ''; return; }
    var tag = 'camp_' + Date.now() + '_' + Math.random().toString(36).substring(2,6);
    var customVars = {
        logo_url: document.getElementById('evLogoUrl')?.value || '',
        link_contato: document.getElementById('evLinkContato').value,
        link_site: document.getElementById('evLinkSite').value,
        titulo_novidade: document.getElementById('evTituloNovidade')?.value || '',
        descricao_novidade: document.getElementById('evDescNovidade')?.value || '',
        desconto: document.getElementById('evDesconto')?.value || '',
        produto: document.getElementById('evProduto')?.value || '',
        cupom: document.getElementById('evCupom')?.value || '',
        titulo_oferta: document.getElementById('evTituloOferta')?.value || '',
        descricao_oferta: document.getElementById('evDescOferta')?.value || '',
        mensagem_adicional: document.getElementById('evMsgAdicional')?.value || '',
    };
    fetch('api/email_api.php?action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ template: tpl, subject: subject, recipients: recipients, custom_vars: customVars, campaign_tag: tag })
    }).then(function(r) { return r.json(); }).then(function(res) {
        btn.disabled = false;
        if (res.success) {
            status.innerHTML = '<span style="color:#27ae60;"> Enviado: ' + res.sent + ' | Falhas: ' + res.failed + '</span>';
            fetch('api/email_save_campaign.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ tag: tag, subject: subject, template: tpl, segments: emailGetSegments().join(', '), total_recipients: recipients.length, sent_count: res.sent })
            });
        } else {
            status.innerHTML = '<span style="color:#e94560;"> Erro: ' + (res.error || 'desconhecido') + '</span>';
        }
    }).catch(function() {
        btn.disabled = false;
        status.innerHTML = '<span style="color:#e94560;"> Erro de conexao com o servidor</span>';
    });
}
</script>

<!-- CLIENTES -->
