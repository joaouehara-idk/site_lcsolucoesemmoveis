<section id="tab-projetos">

<div style="margin-bottom:16px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Projetos Realizados
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Clientes que já compraram ou fizeram projetos com a LC
        </span>
    </h1>
</div>

<div class="cards" id="projStats" style="margin-bottom:16px;">
    <div class="card"><div class="num" id="psTotal">0</div><div class="label">Total Projetos</div></div>
    <div class="card"><div class="num" id="psConcluidos" style="color:#2ecc71;">0</div><div class="label">Concluídos</div></div>
    <div class="card"><div class="num" id="psAndamento" style="color:#3498db;">0</div><div class="label">Em Andamento</div></div>
    <div class="card"><div class="num" id="psClientes" style="color:var(--gold);">0</div><div class="label">Clientes Atendidos</div></div>
    <div class="card"><div class="num" id="psReceita" style="color:#2ecc71;">R$ 0</div><div class="label">Receita Total</div></div>
</div>

<div class="exp-controls">
    <div class="row">
        <label>Filtro</label>
        <select id="projFiltro" onchange="projListCarregar()" style="min-width:140px;">
            <option value="">Todos</option>
            <option value="concluido">Concluídos</option>
            <option value="andamento">Em Andamento</option>
            <option value="orcamento">Orçamento</option>
        </select>
        <input type="text" id="projBusca" placeholder="Buscar cliente ou projeto..." onkeydown="if(event.key==='Enter') projListCarregar()">
        <button class="btn-primary" onclick="projListCarregar()">Carregar</button>
    </div>
</div>

<div class="exp-table-wrap">
    <div class="exp-toolbar" id="projListToolbar" style="display:none;">
        <div class="left">
            <span class="count" id="projListCount"></span>
        </div>
        <div>
            <span style="font-size:12px;color:var(--text-dim);">Projetos concluídos e em andamento</span>
        </div>
    </div>
    <div id="projListContainer">
        <div class="exp-empty"><h3>Carregue a lista de projetos</h3></div>
    </div>
</div>

<script>
function projListCarregar() {
    var status = document.getElementById('projFiltro').value;
    var q = document.getElementById('projBusca').value.trim();
    var url = 'api/projetos.php?action=list&limit=500';
    if (status) {
        if (status === 'andamento') url += '&status=aprovado&status=producao&status=instalacao';
        else url += '&status=' + encodeURIComponent(status);
    }

    document.getElementById('projListContainer').innerHTML = '<div class="exp-empty"><h3>Carregando...</h3></div>';

    Promise.all([
        fetch(url).then(function(r){return r.json()}),
        fetch('api/projetos.php?action=stats').then(function(r){return r.json()})
    ]).then(function(results) {
        var d = results[0], st = results[1];
        var records = d.records || [];

        document.getElementById('projListToolbar').style.display = records.length ? 'flex' : 'none';
        document.getElementById('projListCount').textContent = d.total + ' projetos';

        if (st.success) {
            document.getElementById('psTotal').textContent = st.data.total_projetos;
            document.getElementById('psConcluidos').textContent = st.data.projetos_concluidos;
            document.getElementById('psAndamento').textContent = st.data.em_projeto;
            document.getElementById('psClientes').textContent = st.data.total_clientes;
            document.getElementById('psReceita').textContent = 'R$ ' + parseFloat(st.data.receita).toLocaleString('pt-BR', {minimumFractionDigits:2});
        }

        if (!records.length) {
            document.getElementById('projListContainer').innerHTML = '<div class="exp-empty"><h3>Nenhum projeto encontrado</h3></div>';
            return;
        }

        var html = '<div style="overflow-x:auto;"><table><thead><tr>' +
            '<th>Cliente</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>Status</th><th>Início</th><th>Conclusão</th>' +
            '</tr></thead><tbody>';

        records.forEach(function(p) {
            var stc = p.status === 'concluido' ? 'badge-ativo' : p.status === 'cancelado' ? 'badge-baixada' : 'badge-outro';
            var nome = (p.cliente_nome || 'N/I').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            var desc = (p.descricao || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            var tipo = (p.tipo || '').replace(/_/g,' ').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            var stLabel = (p.status || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            html += '<tr>' +
                '<td><strong>' + nome + '</strong></td>' +
                '<td>' + desc + '</td>' +
                '<td style="font-size:11px;">' + tipo + '</td>' +
                '<td>' + (p.valor ? 'R$ ' + parseFloat(p.valor).toLocaleString('pt-BR', {minimumFractionDigits:2}) : '-') + '</td>' +
                '<td><span class="badge ' + stc + '">' + stLabel + '</span></td>' +
                '<td style="font-size:11px;">' + (p.data_inicio || '-') + '</td>' +
                '<td style="font-size:11px;">' + (p.data_conclusao || '-') + '</td></tr>';
        });
        html += '</tbody></table></div>';
        document.getElementById('projListContainer').innerHTML = html;
    });
}

projListCarregar();
</script>

</section>
