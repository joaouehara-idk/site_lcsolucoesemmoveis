<section id="tab-charts">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Gráficos Comparativos
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Análise de dados por segmento
        </span>
    </h1>
</div>

<?php
$chartSeg = $_GET['cs'] ?? 'arquitetos_ms';
if (!in_array($chartSeg, $segKeys)) $chartSeg = 'arquitetos_ms';
$cd = $chartsData;
$totals = array_map(function($t) { return $t['stats']['total']; }, $cd);
$maxTotal = max($totals) ?: 1;
$cnaeDescriptions = [
    'arquitetos_ms' => '7111-1/00 - Servicos de Arquitetura',
    'designers_interiores_ms' => '7410-2/02 - Design de Interiores',
    'designers_ms' => '7410-2/01 - Design / 7410-2/03 - Design de Produto',
    'construtoras_ms' => '4110-7/00 - Incorporacao / 4120-4/00 - Construcao de Edificios',
    'imobiliarias_ms' => '6821-8/01 - Corretagem / 6822-6/00 - Gestao Imobiliaria',
    'engenheiros_ms' => '7112-0/00 - Servicos de Engenharia',
    'lojas_marcenarias_ms' => '4754-7/01 - Moveis / 3329-5/01 - Montagem / 4330-4/02 - Armarios Embutidos',
    'paisagismo_decoracao_ms' => '8130-3/00 - Paisagismo / 4759-8/01 - Tapecearia / 4754-7/03 - Iluminacao',
];
?>

<div class="charts-page">

<div class="stat-cards">
    <div class="stat-card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'total')), 0, ',', '.') ?></div><div class="label">Total Parceiros</div></div>
    <div class="stat-card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'ativos')), 0, ',', '.') ?></div><div class="label">Ativos</div></div>
    <div class="stat-card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'com_tel')), 0, ',', '.') ?></div><div class="label">Com Telefone</div></div>
    <div class="stat-card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'bairros')), 0, ',', '.') ?></div><div class="label">Bairros (total)</div></div>
</div>

<div class="chart-row chart-full">
    <div class="chart-box">
        <h3>Comparativo: Total de Registros por Segmento</h3>
        <canvas id="comparisonChart"></canvas>
    </div>
</div>

<div class="chart-row">
    <div class="chart-box">
        <h3>Top 10 Bairros</h3>
        <select id="chartSegSelect" onchange="updateChart()">
            <?php foreach ($segments as $key => $s): ?>
            <option value="<?= $key ?>" <?= $key === $chartSeg ? 'selected' : '' ?>><?= $s['label'] ?> (<?= number_format($statsAll[$key]['total'], 0, ',', '.') ?>)</option>
            <?php endforeach; ?>
        </select>
        <canvas id="bairroChart"></canvas>
    </div>
    <div class="chart-box">
        <h3 id="sitChartTitle">Distribuicao por Situacao Cadastral</h3>
        <select id="chartSegSelect2" onchange="updateSitChart(this.value)">
            <?php foreach ($segments as $key => $s): ?>
            <option value="<?= $key ?>" <?= $key === $chartSeg ? 'selected' : '' ?>><?= $s['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <canvas id="sitChart"></canvas>
    </div>
</div>

<div class="chart-row chart-full">
    <div class="chart-box">
        <h3>Cobertura de Telefone por Segmento</h3>
        <canvas id="telChart"></canvas>
    </div>
</div>

<div class="chart-row chart-full">
    <div class="chart-box">
        <h3>Tabela Comparativa de Segmentos</h3>
        <table class="stat-table">
            <thead><tr>
                <th>Segmento</th><th>CNAEs</th><th>Total</th><th>Ativos</th><th>% Ativos</th><th>Com Tel.</th><th>% Tel.</th><th>Bairros</th><th>Barra</th>
            </tr></thead>
            <tbody>
                <?php foreach ($segments as $key => $s):
                    $st = $statsAll[$key];
                    $pctAtivos = $st['total'] > 0 ? round($st['ativos'] / $st['total'] * 100) : 0;
                    $pctTel = $st['total'] > 0 ? round($st['com_tel'] / $st['total'] * 100) : 0;
                    $width = round($st['total'] / $maxTotal * 100);
                ?>
                <tr>
                    <td><strong><?= $s['label'] ?></strong></td>
                    <td style="font-size:11px;color:#888;"><?= $cnaeDescriptions[$key] ?? '' ?></td>
                    <td><strong><?= number_format($st['total'], 0, ',', '.') ?></strong></td>
                    <td><?= number_format($st['ativos'], 0, ',', '.') ?></td>
                    <td><?= $pctAtivos ?>%</td>
                    <td><?= number_format($st['com_tel'], 0, ',', '.') ?></td>
                    <td><?= $pctTel ?>%</td>
                    <td><?= $st['bairros'] ?></td>
                    <td><div class="bar-cell"><div class="bar-fill" style="width:<?= $width ?>%;background:<?= $s['color'] ?>"></div><span style="font-size:11px;color:#888;"><?= $width ?>%</span></div></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<script>
const chartsData = <?= json_encode($chartsData) ?>;
const sitLabels = <?= json_encode($sitLabels) ?>;
const sitColors = ['#1a1a2e','#e94560','#f5a623','#7ed321','#4a90d9'];
const segColorMap = <?= json_encode($segColors) ?>;
const segLabelMap = <?= json_encode($segLabels) ?>;
let bairroChartInst = null;
let sitChartInst = null;

new Chart(document.getElementById('comparisonChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_values($segLabels)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($totals)) ?>,
            backgroundColor: <?= json_encode(array_values($segColors)) ?>,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } }
    }
});

new Chart(document.getElementById('telChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_values($segLabels)) ?>,
        datasets: [
            { label: 'Com telefone', data: <?= json_encode(array_column($statsAll, 'com_tel')) ?>, backgroundColor: '#27ae60', borderRadius: 4 },
            { label: 'Sem telefone', data: <?= json_encode(array_map(function($s) { return $s['total'] - $s['com_tel']; }, $statsAll)) ?>, backgroundColor: '#e0e0e0', borderRadius: 4 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, stacked: true }, x: { stacked: true, ticks: { font: { size: 10 } } } }
    }
});

function updateChart() {
    const seg = document.getElementById('chartSegSelect').value;
    const data = chartsData[seg];
    if (bairroChartInst) bairroChartInst.destroy();
    bairroChartInst = new Chart(document.getElementById('bairroChart'), {
        type: 'bar',
        data: {
            labels: data.bairros.map(function(r) { return r.bairro; }),
            datasets: [{ data: data.bairros.map(function(r) { return parseInt(r.total); }), backgroundColor: segColorMap[seg] || '#1a1a2e', borderRadius: 4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } } }
    });
}

function updateSitChart(seg) {
    if (!seg) seg = document.getElementById('chartSegSelect2').value;
    const data = chartsData[seg];
    if (sitChartInst) sitChartInst.destroy();
    document.getElementById('sitChartTitle').textContent = 'Distribuicao por Situacao Cadastral - ' + (segLabelMap[seg] || seg);
    sitChartInst = new Chart(document.getElementById('sitChart'), {
        type: 'pie',
        data: {
            labels: data.sit.map(function(r) { return sitLabels[r.situacao_cadastral] || r.situacao_cadastral; }),
            datasets: [{ data: data.sit.map(function(r) { return parseInt(r.total); }), backgroundColor: sitColors }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
}

updateChart();
updateSitChart('<?= $chartSeg ?>');
</script>

</section>
<!-- SEGMENT VIEW -->
