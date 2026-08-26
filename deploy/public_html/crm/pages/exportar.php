<section id="tab-exportar">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Exportar Dados
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Selecione, filtre e exporte seus parceiros
        </span>
    </h1>
</div>

<div class="exp-controls">
    <div class="row">
        <label>Segmentos</label>
        <div class="exp-seg-grid" id="expSegGrid">
            <?php foreach ($segments as $key => $s): ?>
            <label><input type="checkbox" value="<?= $key ?>" checked onchange="expUpdateSegments()"><span><?= $s['label'] ?></span></label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="row">
        <label>Filtro</label>
        <select id="expStatus">
            <option value="todos">Todos</option>
            <option value="ativos">Ativos</option>
            <option value="inativos">Inativos</option>
            <option value="com_telefone">Com telefone</option>
            <option value="sem_telefone">Sem telefone</option>
            <option value="ativos_tel">Ativos + Telefone</option>
        </select>
        <input type="text" id="expSearch" placeholder="Buscar por nome, bairro, telefone..." onkeydown="if(event.key==='Enter') expCarregar()">
        <button class="btn-primary" onclick="expCarregar()">Carregar</button>
    </div>
    <div class="row">
        <label>Cidade</label>
        <div class="city-filter">
            <select id="expCidade" placeholder="Filtrar por cidade..." autocomplete="off">
                <option value="">Todas as cidades</option>
                <?php foreach ($cidades as $cod => $nome): ?>
                <option value="<?= $cod ?>"><?= htmlspecialchars($nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="exp-table-wrap">
    <div class="exp-toolbar" id="expToolbar" style="display:none;">
        <div class="left">
            <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;">
                <input type="checkbox" id="expSelectAll" onchange="expToggleAll()"> Selecionar todos
            </label>
            <span class="count" id="expCount"></span>
        </div>
        <div class="exp-export-btns">
            <button class="btn-csv" onclick="expExportCSV()">CSV</button>
            <button class="btn-xlsx" onclick="expExportXLSX()">XLSX</button>
            <button class="btn-pdf" onclick="expExportPDF()">PDF</button>
            <button class="btn-print" onclick="expExportPrint()">Imprimir</button>
        </div>
    </div>
    <div id="expTableContainer">
        <div class="exp-empty" id="expEmpty">
            <h3>Selecione os segmentos e clique em "Carregar"</h3>
            <p>Voce podera selecionar registros individualmente e exportar nos formatos disponiveis.</p>
        </div>
    </div>
    <div class="pagination" id="expPagination" style="display:none;"></div>
</div>

<script>
let expAllData = [];
let expSelected = new Set();
let expTotal = 0;
let expCitySelect = null;

document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('expCidade');
    if (el) expCitySelect = new TomSelect(el, {
        maxItems: 1,
        placeholder: 'Filtrar por cidade...',
        allowEmptyOption: true
    });
});

function expGetSegments() {
    const checks = document.querySelectorAll('#expSegGrid input:checked');
    return Array.from(checks).map(function(c) { return c.value; });
}

function expUpdateSegments() {
    document.getElementById('expToolbar').style.display = 'none';
    document.getElementById('expTableContainer').innerHTML = '<div class="exp-empty" id="expEmpty"><h3>Selecione os segmentos e clique em "Carregar"</h3></div>';
    document.getElementById('expPagination').style.display = 'none';
}

function expCarregar() {
    const segs = expGetSegments();
    if (!segs.length) { alert('Selecione pelo menos um segmento.'); return; }
    const status = document.getElementById('expStatus').value;
    const q = document.getElementById('expSearch').value.trim();
    let url = 'api/export_data.php?segments[]=' + segs.join('&segments[]=') + '&status=' + status + '&q=' + encodeURIComponent(q) + '&limit=5000';
    const cidade = expCitySelect ? expCitySelect.getValue() : '';
    if (cidade) url += '&cidade=' + encodeURIComponent(cidade);

    document.getElementById('expTableContainer').innerHTML = '<div class="exp-empty"><h3>Carregando...</h3></div>';
    document.getElementById('expToolbar').style.display = 'none';
    document.getElementById('expPagination').style.display = 'none';

    fetch(url)
    .then(function(r) { return r.json(); })
    .then(function(data) {
        expAllData = data.records;
        expTotal = data.total;
        expSelected = new Set();
        expRenderTable();
    })
    .catch(function() {
        document.getElementById('expTableContainer').innerHTML = '<div class="exp-empty"><h3>Erro ao carregar dados</h3></div>';
    });
}

function expRenderTable() {
    const container = document.getElementById('expTableContainer');
    const toolbar = document.getElementById('expToolbar');
    const pagination = document.getElementById('expPagination');

    if (!expAllData.length) {
        container.innerHTML = '<div class="exp-empty"><h3>Nenhum registro encontrado</h3></div>';
        toolbar.style.display = 'none';
        pagination.style.display = 'none';
        return;
    }

    toolbar.style.display = 'flex';
    document.getElementById('expCount').textContent = expTotal + ' registros (' + expAllData.length + ' carregados)';
    document.getElementById('expSelectAll').checked = expSelected.size === expAllData.length && expAllData.length > 0;

    var segColors = <?= json_encode($segColors) ?>;

    var html = '<div style="overflow-x:auto;"><table><thead><tr>' +
        '<th class="exp-check-col"></th>' +
        '<th>Segmento</th><th>Razao Social</th><th>Fantasia</th><th>Bairro</th><th>Cidade</th><th>Telefone</th><th>Status</th>' +
        '</tr></thead><tbody>';

    expAllData.forEach(function(row, idx) {
        var segColor = segColors[row.segment] || '#888';
        var checked = expSelected.has(idx) ? 'checked' : '';
        var tel = (row.ddd && row.telefone) ? '(' + row.ddd + ') ' + row.telefone : '-';
        var sit = row.situacao_cadastral === '02' ? '<span class="badge badge-ativo">Ativa</span>' :
                 row.situacao_cadastral === '08' ? '<span class="badge badge-baixada">Baixada</span>' :
                 '<span class="badge badge-outro">' + (row.situacao_cadastral || 'N/I') + '</span>';
        html += '<tr>' +
            '<td class="exp-check-col"><input type="checkbox" ' + checked + ' onchange="expToggle(' + idx + ')"></td>' +
            '<td><span class="exp-seg-badge" style="background:' + segColor + '">' + (row.segment_label || row.segment) + '</span></td>' +
            '<td><strong>' + (row.razao_social || row.nome_fantasia || 'N/I') + '</strong></td>' +
            '<td>' + (row.nome_fantasia || '-') + '</td>' +
            '<td>' + (row.bairro || '-') + '</td>' +
            '<td>' + (row.nome_municipio || '-') + '</td>' +
            '<td>' + tel + '</td>' +
            '<td>' + sit + '</td>' +
            '</tr>';
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function expToggle(idx) {
    if (expSelected.has(idx)) expSelected.delete(idx);
    else expSelected.add(idx);
    document.getElementById('expSelectAll').checked = expSelected.size === expAllData.length;
}

function expToggleAll() {
    var checked = document.getElementById('expSelectAll').checked;
    if (checked) expAllData.forEach(function(_, idx) { expSelected.add(idx); });
    else expSelected.clear();
    expRenderTable();
}

function expGetSelectedData() {
    if (expSelected.size === 0) { alert('Nenhum registro selecionado.'); return null; }
    return Array.from(expSelected).map(function(idx) { return expAllData[idx]; });
}

function expExportCSV() {
    var data = expGetSelectedData();
    if (!data) return;
    var headers = ['Segmento','CNPJ','Razao Social','Nome Fantasia','Bairro','Cidade','Logradouro','Numero','Complemento','CEP','UF','DDD','Telefone','Email','Situacao','Data Inicio','Capital Social'];
    var lines = [headers.join(';')];
    data.forEach(function(r) {
        lines.push([r.segment_label||r.segment, r.cnpj_basico, r.razao_social, r.nome_fantasia, r.bairro, r.nome_municipio, r.logradouro, r.numero, r.complemento, r.cep, r.uf, r.ddd, r.telefone, r.correio_eletronico, r.situacao_cadastral, r.data_inicio_atividade, r.capital_social].join(';'));
    });
    var bom = '\uFEFF';
    var blob = new Blob([bom + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'parceiros_export.csv';
    link.click();
}

function expExportXLSX() {
    var data = expGetSelectedData();
    if (!data) return;
    var wsData = [['Segmento','CNPJ','Razao Social','Nome Fantasia','Bairro','Cidade','Logradouro','Numero','Complemento','CEP','UF','DDD','Telefone','Email','Situacao','Data Inicio','Capital Social']];
    data.forEach(function(r) {
        wsData.push([r.segment_label||r.segment, r.cnpj_basico, r.razao_social, r.nome_fantasia, r.bairro, r.nome_municipio, r.logradouro, r.numero, r.complemento, r.cep, r.uf, r.ddd, r.telefone, r.correio_eletronico, r.situacao_cadastral, r.data_inicio_atividade, r.capital_social]);
    });
    var ws = XLSX.utils.aoa_to_sheet(wsData);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Parceiros');
    XLSX.writeFile(wb, 'parceiros_export.xlsx');
}

function expExportPDF() {
    var data = expGetSelectedData();
    if (!data) return;
    var headers = [['Segmento','Razao Social','Bairro','Cidade','Telefone']];
    var body = data.map(function(r) {
        var tel = (r.ddd && r.telefone) ? '(' + r.ddd + ') ' + r.telefone : '-';
        return [r.segment_label || r.segment, r.razao_social || r.nome_fantasia || 'N/I', r.bairro || '-', r.nome_municipio || '-', tel];
    });
    var doc = new jspdf.jsPDF();
    doc.setFontSize(10);
    doc.text('Parceiros Selecionados - LC Solucoes em Moveis', 14, 15);
    doc.setFontSize(7);
    doc.text('Total: ' + data.length + ' registros', 14, 21);
    doc.autoTable({ head: headers, body: body, startY: 25, styles: { fontSize: 7 }, headStyles: { fillColor: [26, 26, 46] } });
    doc.save('parceiros_export.pdf');
}

function expExportPrint() {
    var data = expGetSelectedData();
    if (!data) return;
    var win = window.open('', '_blank');
    var html = '<html><head><meta charset="utf-8"><title>Parceiros Selecionados</title>';
    html += '<style>body{font-family:sans-serif;font-size:12px;padding:20px;}table{width:100%;border-collapse:collapse;}th{background:#1a1a2e;color:#fff;padding:6px 8px;text-align:left;font-size:10px;}td{padding:5px 8px;border-bottom:1px solid #ddd;}h1{font-size:18px;margin-bottom:5px;}.meta{color:#888;font-size:11px;margin-bottom:15px;}</style></head><body>';
    html += '<h1>Parceiros LC Solucoes em Moveis</h1>';
    html += '<div class="meta">Total: ' + data.length + ' registros selecionados - MS</div>';
    html += '<table><thead><tr><th>Segmento</th><th>CNPJ</th><th>Razao Social</th><th>Fantasia</th><th>Bairro</th><th>Cidade</th><th>Telefone</th><th>Email</th></tr></thead><tbody>';
    data.forEach(function(r) {
        var tel = (r.ddd && r.telefone) ? '(' + r.ddd + ') ' + r.telefone : '-';
        html += '<tr><td>' + (r.segment_label || r.segment) + '</td><td>' + (r.cnpj_basico || '') + '</td><td>' + (r.razao_social || '') + '</td><td>' + (r.nome_fantasia || '-') + '</td><td>' + (r.bairro || '-') + '</td><td>' + (r.nome_municipio || '-') + '</td><td>' + tel + '</td><td>' + (r.correio_eletronico || '-') + '</td></tr>';
    });
    html += '</tbody></table></body></html>';
    win.document.write(html);
    win.document.close();
    win.print();
}
</script>

</section>
<!-- TRACKING -->
