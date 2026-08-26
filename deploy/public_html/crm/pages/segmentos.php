<section id="tab-segmentos">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        Segmentos
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            <?= $cidadeNome ? htmlspecialchars($cidadeNome) : 'Mato Grosso do Sul' ?> — <?= number_format($totalAll, 0, ',', '.') ?> parceiros potenciais
        </span>
    </h1>
</div>

<div class="cards" style="margin-bottom:24px;">
    <div class="card"><div class="num"><?= number_format($totalAll, 0, ',', '.') ?></div><div class="label">Total Parceiros</div></div>
    <div class="card"><div class="num"><?= number_format($totalAtivosAll, 0, ',', '.') ?></div><div class="label">Ativos</div></div>
    <div class="card"><div class="num"><?= number_format($totalTelAll, 0, ',', '.') ?></div><div class="label">Com Telefone</div></div>
    <div class="card"><div class="num"><?= number_format($totalBairrosAll, 0, ',', '.') ?></div><div class="label">Bairros</div></div>
</div>

<div class="seg-cards">
    <?php foreach ($segments as $key => $s):
        $st = $statsAll[$key];
        $pctTel = $st['total'] > 0 ? round($st['com_tel'] / $st['total'] * 100) : 0;
        $pctAtivos = $st['total'] > 0 ? round($st['ativos'] / $st['total'] * 100) : 0;
    ?>
    <a class="seg-card" href="?page=segment&seg=<?= $key ?><?= $cidadeFiltro ? '&cidade=' . urlencode($cidadeFiltro) : '' ?>" style="--seg-color: <?= $s['color'] ?>">
        <div class="top">
            <div class="name">
                <?= $icons[$s['icon']] ?>
                <?= $s['label'] ?>
            </div>
            <span class="chip" style="background:<?= $s['color'] ?>"><?= number_format($st['total'], 0, ',', '.') ?></span>
        </div>
        <div class="total"><?= number_format($st['total'], 0, ',', '.') ?></div>
        <div class="metas">
            <span>&#10004; <?= $st['ativos'] ?> ativos (<?= $pctAtivos ?>%)</span>
            <span>&#9742; <?= $st['com_tel'] ?> (<?= $pctTel ?>%)</span>
            <span>&#128205; <?= $st['bairros'] ?> bairros</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<div class="exp-controls" style="margin-top:24px;">
    <h3 style="font-family:var(--font-heading);font-size:14px;color:var(--gold);margin-bottom:16px;font-weight:700;">Tabela Comparativa</h3>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr>
                <th>Segmento</th><th>Total</th><th>Ativos</th><th>% Ativos</th><th>Com Tel.</th><th>% Tel.</th><th>Bairros</th>
            </tr></thead>
            <tbody>
                <?php foreach ($segments as $key => $s):
                    $st = $statsAll[$key];
                    $pctAtivos = $st['total'] > 0 ? round($st['ativos'] / $st['total'] * 100) : 0;
                    $pctTel = $st['total'] > 0 ? round($st['com_tel'] / $st['total'] * 100) : 0;
                ?>
                <tr onclick="window.location='?page=segment&seg=<?= $key ?><?= $cidadeFiltro ? '&cidade=' . urlencode($cidadeFiltro) : '' ?>'" style="cursor:pointer;">
                    <td><strong><?= $s['label'] ?></strong></td>
                    <td><?= number_format($st['total'], 0, ',', '.') ?></td>
                    <td><?= number_format($st['ativos'], 0, ',', '.') ?></td>
                    <td><?= $pctAtivos ?>%</td>
                    <td><?= number_format($st['com_tel'], 0, ',', '.') ?></td>
                    <td><?= $pctTel ?>%</td>
                    <td><?= $st['bairros'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</section>
