<section id="tab-funil">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Funil de Prospecção
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            Por segmento e disponibilidade
        </span>
    </h1>
</div>

<?php
$funnelTotal = array_sum(array_column($statsAll, 'total'));
$funnelAtivos = array_sum(array_column($statsAll, 'ativos'));
$funnelTel = array_sum(array_column($statsAll, 'com_tel'));
$funnelAptos = 0;
foreach ($segments as $table => $s) {
    $w = $cidadeWhere ? "WHERE $cidadeWhere AND" : 'WHERE';
    $r = $conn->query("SELECT COUNT(*) FROM `$table` $w situacao_cadastral = '02' AND telefone IS NOT NULL AND telefone != ''");
    $funnelAptos += (int)$r->fetch_row()[0];
}
?>

<div class="funil-top">
    <div class="card"><div class="num"><?= number_format($funnelTotal, 0, ',', '.') ?></div><div class="label">Total</div></div>
    <div class="card"><div class="num"><?= number_format($funnelAtivos, 0, ',', '.') ?></div><div class="label">Ativos</div></div>
    <div class="card"><div class="num"><?= number_format($funnelTel, 0, ',', '.') ?></div><div class="label">Com Telefone</div></div>
    <div class="card"><div class="num"><?= number_format($funnelAptos, 0, ',', '.') ?></div><div class="label">Aptos (Ativo + Tel)</div></div>
</div>

<div class="funil-grid">
    <?php foreach ($segments as $table => $seg):
        $st = $statsAll[$table];
        $w = $cidadeWhere ? "WHERE $cidadeWhere AND" : 'WHERE';
        $stAptos = $conn->query("SELECT COUNT(*) FROM `$table` $w situacao_cadastral = '02' AND telefone IS NOT NULL AND telefone != ''")->fetch_row()[0];
        $pctAtivos = $st['total'] > 0 ? round($st['ativos'] / $st['total'] * 100) : 0;
        $pctTel = $st['total'] > 0 ? round($st['com_tel'] / $st['total'] * 100) : 0;
        $pctAptos = $st['total'] > 0 ? round($stAptos / $st['total'] * 100) : 0;
        $dropoff1 = $st['total'] > 0 ? round((1 - $st['ativos'] / $st['total']) * 100) : 0;
        $dropoff2 = $st['ativos'] > 0 ? round((1 - $st['com_tel'] / $st['ativos']) * 100) : 0;
        $dropoff3 = $st['com_tel'] > 0 ? round((1 - $stAptos / $st['com_tel']) * 100) : 0;
    ?>
    <div class="funil-card">
        <h3>
            <?= $icons[$seg['icon']] ?>
            <?= $seg['label'] ?>
            <span class="badge" style="background:<?= $seg['color'] ?>"><?= number_format($st['total'], 0, ',', '.') ?></span>
        </h3>
        <div class="funil-stage">
            <div class="bar-label">Total</div>
            <div class="bar-wrap">
                <div class="bar-fill-funil" style="width:100%;background:<?= $seg['color'] ?>"><?= number_format($st['total'], 0, ',', '.') ?></div>
            </div>
            <div class="bar-pct">100%</div>
        </div>
        <div class="funil-dropoff"><?= $dropoff1 ?>% nao estao ativos</div>
        <div class="funil-stage">
            <div class="bar-label">Ativos</div>
            <div class="bar-wrap">
                <div class="bar-fill-funil" style="width:<?= $pctAtivos ?>%;background:<?= $seg['color'] ?>;opacity:0.85"><?= number_format($st['ativos'], 0, ',', '.') ?></div>
            </div>
            <div class="bar-pct"><?= $pctAtivos ?>%</div>
        </div>
        <div class="funil-dropoff"><?= $dropoff2 ?>% dos ativos sem telefone</div>
        <div class="funil-stage">
            <div class="bar-label">Com Telefone</div>
            <div class="bar-wrap">
                <div class="bar-fill-funil" style="width:<?= $pctTel ?>%;background:<?= $seg['color'] ?>;opacity:0.7"><?= number_format($st['com_tel'], 0, ',', '.') ?></div>
            </div>
            <div class="bar-pct"><?= $pctTel ?>%</div>
        </div>
        <div class="funil-dropoff"><?= $dropoff3 ?>% sem situacao ativa</div>
        <div class="funil-stage">
            <div class="bar-label" style="font-weight:600;color:#1a1a2e;">Aptos</div>
            <div class="bar-wrap" style="background:#e8f5e9;">
                <div class="bar-fill-funil" style="width:<?= $pctAptos ?>%;background:#27ae60"><?= number_format($stAptos, 0, ',', '.') ?></div>
            </div>
            <div class="bar-pct" style="font-weight:600;"><?= $pctAptos ?>%</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</section>
<!-- CHARTS -->
