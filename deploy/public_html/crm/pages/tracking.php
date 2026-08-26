<section id="tab-tracking">

<div class="exp-controls" style="margin-bottom:16px;">
    <h2 style="font-family:var(--font-heading);margin:0 0 6px;font-size:20px;color:var(--gold);font-weight:800;">Tracking de Email</h2>
    <p style="margin:0 0 16px;font-size:13px;color:var(--text-dim);">Visualizações, cliques e conversões das campanhas de email marketing</p>
    <div class="row" style="gap:10px;flex-wrap:wrap;">
        <button class="btn-primary" onclick="trackCarregar()">Atualizar</button>
        <button class="btn-primary" onclick="trackBrevoSync()" style="background:linear-gradient(135deg,#0092ff,#0066cc)!important;color:#fff!important;" id="trackBrevoBtn"><i class="ph-bold ph-cloud-arrow-down"></i> Sincronizar Brevo</button>
        <button class="btn-primary" onclick="trackBrevoConfig()" style="background:rgba(0,146,255,0.15)!important;color:#0092ff!important;" id="trackBrevoConfigBtn"><i class="ph-bold ph-gear"></i></button>
        <span id="trackBrevoStatus" style="font-size:11px;color:var(--text-dim);align-self:center;"></span>
        <span id="trackStatus" style="font-size:12px;color:var(--text-muted);align-self:center;"></span>
    </div>
</div>

<div class="cards" style="margin-bottom:16px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
    <div class="card" style="text-align:center;">
        <div class="num" style="color:#2ecc71;font-size:28px;" id="trackDelivered">0</div>
        <div class="label">Entregues</div>
    </div>
    <div class="card" style="text-align:center;">
        <div class="num" style="color:#3498db;font-size:28px;" id="trackOpens">0</div>
        <div class="label">Visualizações</div>
    </div>
    <div class="card" style="text-align:center;">
        <div class="num" style="color:#f39c12;font-size:28px;" id="trackClicks">0</div>
        <div class="label">Cliques</div>
    </div>
    <div class="card" style="text-align:center;">
        <div class="num" style="color:#e74c3c;font-size:28px;" id="trackBounces">0</div>
        <div class="label">Rejeições</div>
    </div>
    <div class="card" style="text-align:center;">
        <div class="num" style="color:#e94560;font-size:28px;" id="trackSpam">0</div>
        <div class="label">Spam</div>
    </div>
</div>

<div class="exp-controls" style="margin-bottom:16px;">
    <h3 style="font-family:var(--font-heading);font-size:14px;color:var(--gold);margin-bottom:16px;font-weight:700;">Gráfico Diário (últimos 30 dias)</h3>
    <div style="position:relative;height:220px;">
        <canvas id="trackDailyChart" style="background:var(--bg-dark);border-radius:var(--radius-md);"></canvas>
    </div>
</div>

    <div class="exp-controls" style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--font-heading);font-size:14px;color:var(--gold);font-weight:700;margin:0;">Campanhas</h3>
            <button class="btn-primary" onclick="trackAbrirForm()" style="padding:6px 14px;font-size:12px;">+ Nova Campanha</button>
        </div>
        <div style="overflow-x:auto;">
    <table>
        <thead><tr>
            <th>Campanha</th><th>Assunto</th><th>Enviados</th><th>Entregues</th>
            <th>Aberturas</th><th>Cliques</th><th>Rejeições</th><th>Taxa Abert.</th><th>Taxa Click</th><th style="width:50px;">Ação</th>
        </tr></thead>
        <tbody id="trackCampaignsBody">
            <tr><td colspan="10" style="text-align:center;color:var(--text-dim);">Nenhuma campanha ainda</td></tr>
        </tbody>
    </table>
    </div>
</div>

<div class="exp-controls">
    <h3 style="font-family:var(--font-heading);font-size:14px;color:var(--gold);margin-bottom:16px;font-weight:700;">Eventos Recentes</h3>
    <div style="overflow-x:auto;">
    <table>
        <thead><tr><th>Data</th><th>Evento</th><th>Email</th><th>Campanha</th><th>Link</th></tr></thead>
        <tbody id="trackEventsBody">
            <tr><td colspan="5" style="text-align:center;color:var(--text-dim);">Nenhum evento ainda</td></tr>
        </tbody>
    </table>
    </div>
</div>


<script>
let trackDailyChart = null;

function trackCarregar() {
    document.getElementById('trackStatus').textContent = 'Carregando...';
    trackCarregarOverview();
    trackCarregarCampanhas();
    trackCarregarEventos();
    trackCarregarDaily();
}

function trackCarregarOverview() {
    fetch('api/email_stats.php?action=overview&_=' + Date.now()).then(function(r){return r.json()}).then(function(d){
        if (d.success && d.data) {
            document.getElementById('trackDelivered').textContent = d.data.delivered || 0;
            document.getElementById('trackOpens').textContent = d.data.opens || 0;
            document.getElementById('trackClicks').textContent = d.data.clicks || 0;
            document.getElementById('trackBounces').textContent = d.data.bounces || 0;
            document.getElementById('trackSpam').textContent = d.data.spam || 0;
            document.getElementById('trackStatus').textContent = 'Atualizado: ' + new Date().toLocaleTimeString('pt-BR');
        }
    });
}

function trackCarregarCampanhas() {
    fetch('api/email_stats.php?action=campaigns&_=' + Date.now()).then(function(r){return r.json()}).then(function(d){
        var body = document.getElementById('trackCampaignsBody');
        if (!d.success || !d.campaigns || !d.campaigns.length) {
            body.innerHTML = '<tr><td colspan="9" style="text-align:center;color:#888;">Nenhuma campanha ainda</td></tr>';
            return;
        }
        body.innerHTML = d.campaigns.map(function(c) {
            return '<tr><td><strong>' + (c.tag || '-') + '</strong></td><td style="font-size:12px;">' + (c.subject || '-') + '</td><td>' + (c.sent_count || 0) + '</td><td>' + (c.delivered || 0) + '</td><td>' + (c.opens || 0) + '</td><td>' + (c.clicks || 0) + '</td><td>' + (c.bounces || 0) + '</td><td>' + (c.open_rate || 0) + '%</td><td>' + (c.click_rate || 0) + '%</td><td style="text-align:center;white-space:nowrap;"><button class="btn-small" onclick="trackEditarCampanha(' + c.id + ')" title="Editar campanha" style="background:rgba(52,152,219,0.15);color:#3498db;margin-right:4px;"><i class="ph-bold ph-pencil"></i></button><button class="btn-small btn-danger" onclick="trackExcluirCampanha(' + c.id + ', this)" title="Excluir campanha"><i class="ph-bold ph-trash"></i></button></td></tr>';
        }).join('');
    });
}

function trackCarregarEventos() {
    fetch('api/email_stats.php?action=events&limit=30&_=' + Date.now()).then(function(r){return r.json()}).then(function(d){
        var body = document.getElementById('trackEventsBody');
        if (!d.success || !d.events || !d.events.length) {
            body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#888;">Nenhum evento ainda</td></tr>';
            return;
        }
        body.innerHTML = d.events.map(function(e) {
            return '<tr><td style="font-size:11px;">' + e.ts + '</td><td>' + e.event_type + '</td><td style="font-size:11px;">' + e.email + '</td><td style="font-size:11px;">' + (e.campaign_tag || '-') + '</td><td style="font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;">' + (e.link_url ? '<a href="' + e.link_url + '" target="_blank" style="color:#C5A253;">' + e.link_url.substring(0,40) + '...</a>' : '-') + '</td></tr>';
        }).join('');
    });
}

var trackChartConfig = {
    type: 'line',
    data: { labels: [], datasets: [
        {label:'Entregues', data:[], borderColor:'#2ecc71', backgroundColor:'rgba(46,204,113,0.1)', fill:true, tension:0.3, pointRadius:2, pointHitRadius:10},
        {label:'Aberturas', data:[], borderColor:'#3498db', backgroundColor:'rgba(52,152,219,0.1)', fill:true, tension:0.3, pointRadius:2, pointHitRadius:10},
        {label:'Cliques', data:[], borderColor:'#f39c12', backgroundColor:'rgba(243,156,18,0.1)', fill:true, tension:0.3, pointRadius:2, pointHitRadius:10},
    ]},
    options: {
        responsive: true, maintainAspectRatio: false, animation: false, resizeDelay: 500,
        plugins: {legend:{labels:{color:'#888',font:{size:11}}}},
        scales: { x:{ticks:{color:'#666',font:{size:10}},grid:{color:'rgba(255,255,255,0.03)'}}, y:{ticks:{color:'#666',font:{size:10}},grid:{color:'rgba(255,255,255,0.05)'},beginAtZero:true} }
    }
};

function trackCarregarDaily() {
    fetch('api/email_stats.php?action=daily&days=30&_=' + Date.now()).then(function(r){return r.json()}).then(function(d){
        if (!d.success || !d.daily) return;
        var labels = d.daily.map(function(r){ return r.date.substring(5); });
        var opens = d.daily.map(function(r){ return parseInt(r.opens); });
        var clicks = d.daily.map(function(r){ return parseInt(r.clicks); });
        var delivered = d.daily.map(function(r){ return parseInt(r.delivered); });

        if (trackDailyChart) {
            trackDailyChart.data.labels = labels;
            trackDailyChart.data.datasets[0].data = delivered;
            trackDailyChart.data.datasets[1].data = opens;
            trackDailyChart.data.datasets[2].data = clicks;
            trackDailyChart.update('none');
        } else {
            trackChartConfig.data.labels = labels;
            trackChartConfig.data.datasets[0].data = delivered;
            trackChartConfig.data.datasets[1].data = opens;
            trackChartConfig.data.datasets[2].data = clicks;
            trackDailyChart = new Chart(document.getElementById('trackDailyChart').getContext('2d'), trackChartConfig);
        }
    });
}

function trackExcluirCampanha(id, btn) {
    if (!confirm('Excluir esta campanha e todos os eventos associados?')) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="ph-bold ph-spinner"></i>';
    fetch('api/email_save_campaign.php?action=delete&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (d.success) {
            trackCarregar();
        } else {
            alert('Erro: ' + (d.error || 'desconhecido'));
            btn.disabled = false;
            btn.innerHTML = '<i class="ph-bold ph-trash"></i>';
        }
    });
}

function trackAbrirForm(camp) {
    document.getElementById('trackFormId').value = camp ? (camp.id || 0) : 0;
    document.getElementById('trackFormTag').value = camp ? (camp.tag || '') : '';
    document.getElementById('trackFormSubject').value = camp ? (camp.subject || '') : '';
    document.getElementById('trackFormTemplate').value = camp ? (camp.template || '') : '';
    document.getElementById('trackFormSegments').value = camp ? (camp.segments || '') : '';
    document.getElementById('trackFormTotal').value = camp ? (camp.total_recipients || 0) : 0;
    document.getElementById('trackFormSent').value = camp ? (camp.sent_count || 0) : 0;
    document.getElementById('trackFormModal').style.display = 'flex';
}
function trackFecharForm() {
    document.getElementById('trackFormModal').style.display = 'none';
}
function trackSalvarCampanha() {
    var id = parseInt(document.getElementById('trackFormId').value) || 0;
    var tag = document.getElementById('trackFormTag').value.trim();
    var subject = document.getElementById('trackFormSubject').value.trim();
    var template = document.getElementById('trackFormTemplate').value.trim();
    var segments = document.getElementById('trackFormSegments').value.trim();
    var total = parseInt(document.getElementById('trackFormTotal').value) || 0;
    var sent = parseInt(document.getElementById('trackFormSent').value) || 0;
    if (!tag) { alert('Tag é obrigatória'); return; }
    if (!subject) { alert('Assunto é obrigatório'); return; }
    var btn = document.querySelector('#trackFormModal .btn-primary');
    btn.disabled = true; btn.textContent = 'Salvando...';
    fetch('api/email_save_campaign.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tag: tag, subject: subject, template: template, segments: segments, total_recipients: total, sent_count: sent })
    }).then(function(r){return r.json()}).then(function(d){
        btn.disabled = false; btn.textContent = 'Salvar';
        if (d.success) { trackFecharForm(); trackCarregar(); }
        else { alert('Erro: ' + (d.error || 'desconhecido')); }
    });
}
/* ─── Brevo Sync ─── */

function trackBrevoStatus() {
    fetch('api/email_brevo_sync.php?action=status').then(function(r){return r.json()}).then(function(d){
        var el = document.getElementById('trackBrevoStatus');
        if (d.connected) {
            el.innerHTML = '<span style="color:#2ecc71;">Brevo: <strong>' + d.account_name + '</strong></span>';
            document.getElementById('trackBrevoBtn').style.display = 'inline-flex';
        } else if (d.configured) {
            el.innerHTML = '<span style="color:#e74c3c;">Brevo: conexao falhou</span>';
        } else {
            el.innerHTML = '<span style="color:#888;">Brevo: nao configurado</span>';
            document.getElementById('trackBrevoBtn').style.display = 'none';
        }
    });
}

function trackBrevoConfig() {
    fetch('api/email_brevo_sync.php?action=status').then(function(r){return r.json()}).then(function(d){
        document.getElementById('trackBrevoApiKey').value = d.configured ? '[configurada]' : '';
        document.getElementById('trackBrevoModal').style.display = 'flex';
        document.getElementById('trackBrevoModalStatus').textContent = '';
    });
}

function trackBrevoFechar() {
    document.getElementById('trackBrevoModal').style.display = 'none';
}

function trackBrevoSalvar() {
    var key = document.getElementById('trackBrevoApiKey').value.trim();
    if (!key || key === '[configurada]') {
        trackBrevoSync();
        trackBrevoFechar();
        return;
    }
    var btn = document.querySelector('#trackBrevoModal .btn-primary');
    btn.disabled = true;
    btn.textContent = 'Salvando...';
    document.getElementById('trackBrevoModalStatus').textContent = 'Salvando chave...';

    fetch('api/email_brevo_sync.php?action=save_config', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({api_key: key})
    }).then(function(r){return r.json()}).then(function(d){
        btn.disabled = false;
        btn.textContent = 'Salvar & Sincronizar';
        if (d.success) {
            trackBrevoFechar();
            trackBrevoSync();
        } else {
            document.getElementById('trackBrevoModalStatus').textContent = 'Erro: ' + d.error;
        }
    });
}

function trackBrevoSync() {
    var btn = document.getElementById('trackBrevoBtn');
    var status = document.getElementById('trackBrevoStatus');
    btn.disabled = true;
    btn.innerHTML = '<i class="ph-bold ph-spinner"></i> Sincronizando...';

    var days = 7;
    var daysEl = document.getElementById('trackBrevoDays');
    if (daysEl) days = daysEl.value;

    status.innerHTML = '<span style="color:#888;">Buscando eventos dos ultimos ' + days + ' dias...</span>';

    fetch('api/email_brevo_sync.php?action=sync&days=' + days + '&limit=5000').then(function(r){return r.json()}).then(function(d){
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-bold ph-cloud-arrow-down"></i> Sincronizar Brevo';
        if (d.success) {
            status.innerHTML = '<span style="color:#2ecc71;">Sincronizado: ' + d.imported + ' novos eventos de ' + d.total_api + '</span>';
            trackCarregar();
        } else {
            status.innerHTML = '<span style="color:#e74c3c;">Erro: ' + (d.error || 'desconhecido') + '</span>';
        }
    }).catch(function(){
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-bold ph-cloud-arrow-down"></i> Sincronizar Brevo';
        status.innerHTML = '<span style="color:#e74c3c;">Erro de conexao</span>';
    });
}

function trackEditarCampanha(id) {
    fetch('api/email_save_campaign.php?action=get&id=' + id).then(function(r){return r.json()}).then(function(d){
        if (d.success && d.campaign) trackAbrirForm(d.campaign);
        else alert('Erro ao carregar campanha');
    });
}

setTimeout(function() { trackCarregar(); trackBrevoStatus(); }, 500);
</script>

<!-- Modal Brevo Config -->
<div id="trackBrevoModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)trackBrevoFechar()">
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:24px;width:500px;max-width:90vw;border:1px solid var(--border);" onclick="event.stopPropagation()">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--font-heading);font-size:16px;color:var(--gold);font-weight:800;margin:0;">Brevo API</h3>
            <button onclick="trackBrevoFechar()" style="background:none;border:none;color:var(--text-dim);font-size:20px;cursor:pointer;"><i class="ph-bold ph-x"></i></button>
        </div>
        <p style="font-size:12px;color:var(--text-dim);margin:0 0 16px;">Sincronize eventos de e-mail (aberturas, cliques, rejeic&otilde;es) da Brevo para o CRM.</p>
        <div style="display:grid;gap:12px;">
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Chave API v3</label>
                <input type="text" id="trackBrevoApiKey" placeholder="xkeysib-..." style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;font-size:12px;">
                <div style="font-size:10px;color:#888;margin-top:4px;">Obtenha em <a href="https://app.brevo.com/settings/keys/api" target="_blank" style="color:#0092ff;">Brevo &gt; API Keys</a></div>
            </div>
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Buscar &uacute;ltimos (dias)</label>
                <select id="trackBrevoDays" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
                    <option value="1">1 dia</option>
                    <option value="3">3 dias</option>
                    <option value="7" selected>7 dias</option>
                    <option value="15">15 dias</option>
                    <option value="30">30 dias</option>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <span id="trackBrevoModalStatus" style="font-size:12px;color:var(--text-muted);align-self:center;flex:1;"></span>
            <button class="btn-primary" onclick="trackBrevoSalvar()" style="padding:10px 20px;background:#0092ff!important;color:#fff!important;">Salvar &amp; Sincronizar</button>
            <button class="btn-primary" onclick="trackBrevoFechar()" style="padding:10px 20px;background:transparent!important;color:var(--text-muted)!important;border:1px solid var(--border)!important;">Cancelar</button>
        </div>
    </div>
</div>

<!-- Modal Campanha -->
<div id="trackFormModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)trackFecharForm()">
    <div style="background:var(--bg-card);border-radius:var(--radius-xl);padding:24px;width:520px;max-width:90vw;border:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--font-heading);font-size:16px;color:var(--gold);font-weight:800;margin:0;" id="trackFormTitle">Nova Campanha</h3>
            <button onclick="trackFecharForm()" style="background:none;border:none;color:var(--text-dim);font-size:20px;cursor:pointer;"><i class="ph-bold ph-x"></i></button>
        </div>
        <input type="hidden" id="trackFormId">
        <div style="display:grid;gap:12px;">
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Tag</label>
                <input type="text" id="trackFormTag" placeholder="camp_parceiros_2026" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
            </div>
            <div>
                <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Assunto</label>
                <input type="text" id="trackFormSubject" placeholder="Parceria LC Moveis" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Template</label>
                    <input type="text" id="trackFormTemplate" placeholder="parceiros.html" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Segmentos</label>
                    <input type="text" id="trackFormSegments" placeholder="arquitetos, engenheiros" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Total Destinatários</label>
                    <input type="number" id="trackFormTotal" placeholder="0" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Enviados</label>
                    <input type="number" id="trackFormSent" placeholder="0" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:rgba(0,0,0,0.4);color:var(--text);outline:none;margin-top:4px;">
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <button class="btn-primary" onclick="trackSalvarCampanha()" style="padding:10px 24px;">Salvar</button>
            <button class="btn-primary" onclick="trackFecharForm()" style="padding:10px 24px;background:transparent!important;color:var(--text-muted)!important;border:1px solid var(--border)!important;">Cancelar</button>
        </div>
    </div>
</div>

</section>
<!-- EMAIL -->
