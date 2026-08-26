<section id="tab-dashboard">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Dashboard
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            <?= $cidadeNome ? htmlspecialchars($cidadeNome) : 'Mato Grosso do Sul' ?>
        </span>
    </h1>
</div>

<div class="cards">
    <div class="card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'total')), 0, ',', '.') ?></div><div class="label">Total Parceiros Potenciais</div></div>
    <div class="card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'ativos')), 0, ',', '.') ?></div><div class="label">Ativos</div></div>
    <div class="card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'com_tel')), 0, ',', '.') ?></div><div class="label">Com Telefone</div></div>
    <div class="card"><div class="num"><?= number_format(array_sum(array_column($statsAll, 'bairros')), 0, ',', '.') ?></div><div class="label">Regioes</div></div>
</div>

<div class="seg-cards">
    <?php foreach ($segments as $key => $s):
        $st = $statsAll[$key];
        $pctTel = $st['total'] > 0 ? round($st['com_tel'] / $st['total'] * 100) : 0;
    ?>
    <a class="seg-card" href="<?= segUrl($key) ?>" style="--seg-color: <?= $s['color'] ?>">
        <div class="top">
            <div class="name"><?= $icons[$s['icon']] ?> <?= $s['label'] ?></div>
            <span class="chip" style="background:<?= $s['color'] ?>"><?= number_format($st['total'], 0, ',', '.') ?></span>
        </div>
        <div class="total"><?= number_format($st['total'], 0, ',', '.') ?></div>
        <div class="metas">
            <span>&#10004; <?= $st['ativos'] ?> ativos</span>
            <span>&#9742; <?= $st['com_tel'] ?> (<?= $pctTel ?>%)</span>
            <span>&#128205; <?= $st['bairros'] ?> bairros</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<?php
$w = $cidadeWhere ? "WHERE $cidadeWhere" : 'WHERE 1=1';
$bairrosR = $conn->query("SELECT bairro, COUNT(*) as total FROM estabelecimentos_ms $w AND bairro IS NOT NULL AND bairro != '' GROUP BY bairro ORDER BY total DESC LIMIT 10");
$bairros = $bairrosR ? $bairrosR->fetch_all(MYSQLI_ASSOC) : [];
$sitR = $conn->query("SELECT situacao_cadastral, COUNT(*) as total FROM estabelecimentos_ms $w GROUP BY situacao_cadastral");
$sit = $sitR ? $sitR->fetch_all(MYSQLI_ASSOC) : [];
?>
<div class="charts">
    <div class="chart-box">
        <h3>Top 10 Bairros<?= $cidadeNome ? " - $cidadeNome" : ' (MS)' ?></h3>
        <canvas id="bairrosChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Situacao Cadastral<?= $cidadeNome ? " - $cidadeNome" : ' (MS)' ?></h3>
        <canvas id="sitChart"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('bairrosChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($bairros, 'bairro')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($bairros, 'total')) ?>,
            backgroundColor: '#1a1a2e',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 10 } }, x: { ticks: { font: { size: 10 } } } }
    }
});
new Chart(document.getElementById('sitChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_map(function($s) { global $sitLabels; return $sitLabels[$s['situacao_cadastral']] ?? $s['situacao_cadastral']; }, $sit)) ?>,
        datasets: [{
            data: <?= json_encode(array_column($sit, 'total')) ?>,
            backgroundColor: ['#1a1a2e','#e94560','#f5a623','#7ed321','#4a90d9']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>

</section>
<!-- FUNIL -->
