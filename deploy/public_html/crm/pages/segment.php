<section id="tab-segment">

<div style="margin-bottom:20px;">
    <h1 style="font-family:var(--font-heading);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-0.3px;">
        <?= $currentSeg['label'] ?>
        <span style="font-size:12px;font-weight:400;color:var(--text-dim);margin-left:10px;font-family:var(--font-body);">
            <?= $cidadeNome ? htmlspecialchars($cidadeNome) : 'Mato Grosso do Sul' ?>
        </span>
    </h1>
</div>

<?php
$activeFilters = [];
if ($filtro !== 'todos') {
    if ($filtro === 'ativos') $activeFilters[] = 'Ativos';
    elseif ($filtro === 'com_telefone' || $filtro === 'tel') $activeFilters[] = 'Com Telefone';
    elseif ($filtro === 'sem_telefone' || $filtro === 'semtel') $activeFilters[] = 'Sem Telefone';
    elseif ($filtro === 'baixadas') $activeFilters[] = 'Baixadas';
    elseif ($filtro === 'email') $activeFilters[] = 'Com Email';
}
if ($bairroFiltro) $activeFilters[] = "Bairro: $bairroFiltro";
if ($capMin !== '') $activeFilters[] = 'Capital >= R$ ' . number_format(floatval($capMin), 0, ',', '.');
if ($capMax !== '') $activeFilters[] = 'Capital <= R$ ' . number_format(floatval($capMax), 0, ',', '.');
$qfUrl = "?page=segment&seg=$segTable";
if ($cidadeFiltro) $qfUrl .= '&cidade=' . urlencode($cidadeFiltro);
$bairrosRes = $conn->query("SELECT bairro, COUNT(*) as total FROM `$segTable` WHERE bairro IS NOT NULL AND bairro != ''" . ($cidadeWhere ? " AND $cidadeWhere" : '') . " GROUP BY bairro ORDER BY total DESC LIMIT 20");
$bairrosList = $bairrosRes ? $bairrosRes->fetch_all(MYSQLI_ASSOC) : [];
$capRange = $conn->query("SELECT MIN(CAST(REPLACE(COALESCE(capital_social,'0'), ',', '.') AS DECIMAL(12,2))) as min_cap, MAX(CAST(REPLACE(COALESCE(capital_social,'0'), ',', '.') AS DECIMAL(12,2))) as max_cap FROM `$segTable`" . ($cidadeWhere ? " WHERE $cidadeWhere" : ''));
$capRow = $capRange ? $capRange->fetch_assoc() : [];
$capMinVal = floatval($capRow['min_cap'] ?? 0);
$capMaxVal = floatval($capRow['max_cap'] ?? 10000000);
?>

<div class="search-bar">
    <form method="GET" style="display:flex;gap:8px;width:100%;flex-wrap:wrap;">
        <input type="hidden" name="page" value="segment">
        <input type="hidden" name="seg" value="<?= $segTable ?>">
        <?php if ($cidadeFiltro): ?><input type="hidden" name="cidade" value="<?= $cidadeFiltro ?>"><?php endif; ?>
        <input type="text" name="q" placeholder="Buscar por nome, fantasia, bairro, CNPJ..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="ph-bold ph-magnifying-glass"></i> Buscar</button>
        <a href="<?= segUrl($segTable) ?>" class="btn-outline"><i class="ph-bold ph-x-circle"></i> Limpar</a>
    </form>
</div>

<?php if ($activeFilters): ?>
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
    <?php foreach ($activeFilters as $af): ?>
    <span style="background:rgba(197,162,83,0.12);color:var(--gold);padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:4px;"><i class="ph-bold ph-funnel" style="font-size:12px;"></i> <?= $af ?></span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="quick-filters">
    <a href="<?= $qfUrl ?>" class="qf-btn <?= (!isset($_GET['f']) || $filtro === 'todos') && !$bairroFiltro && $capMin === '' && $capMax === '' ? 'active' : '' ?>"><i class="ph-bold ph-list-dashes"></i> Todos</a>
    <a href="<?= $qfUrl ?>&f=ativos<?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>" class="qf-btn <?= $filtro === 'ativos' ? 'active' : '' ?>"><i class="ph-bold ph-check-circle"></i> Ativos</a>
    <a href="<?= $qfUrl ?>&f=com_telefone<?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>" class="qf-btn <?= $filtro === 'com_telefone' || $filtro === 'tel' ? 'active' : '' ?>"><i class="ph-bold ph-phone"></i> Com Telefone</a>
    <a href="<?= $qfUrl ?>&f=sem_telefone<?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>" class="qf-btn <?= $filtro === 'sem_telefone' || $filtro === 'semtel' ? 'active' : '' ?>"><i class="ph-bold ph-phone-slash"></i> Sem Telefone</a>
    <a href="<?= $qfUrl ?>&f=email<?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>" class="qf-btn <?= $filtro === 'email' ? 'active' : '' ?>"><i class="ph-bold ph-at"></i> Com Email</a>
    <a href="<?= $qfUrl ?>&f=baixadas<?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>" class="qf-btn <?= $filtro === 'baixadas' ? 'active' : '' ?>"><i class="ph-bold ph-x-square"></i> Baixadas</a>
</div>

<div style="display:flex;gap:16px;flex-wrap:wrap;margin:16px 0;">
    <div class="filter-bar" style="flex:1;min-width:180px;">
        <div class="label"><i class="ph-bold ph-map-pin"></i> Bairro</div>
        <select onchange="if(this.value) window.location='<?= $qfUrl ?>&bairro='+encodeURIComponent(this.value)+'<?= $filtro !== 'todos' ? '&f=' . $filtro : '' ?><?= $capMin !== '' ? '&cap_min=' . $capMin : '' ?><?= $capMax !== '' ? '&cap_max=' . $capMax : '' ?>'">
            <option value="">Todos os bairros</option>
            <?php foreach ($bairrosList as $b): ?>
            <option value="<?= htmlspecialchars($b['bairro']) ?>" <?= $bairroFiltro === $b['bairro'] ? 'selected' : '' ?>><?= htmlspecialchars($b['bairro']) ?> (<?= number_format($b['total'], 0, ',', '.') ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-bar" style="flex:1;min-width:160px;">
        <div class="label"><i class="ph-bold ph-currency-circle-dollar"></i> Capital Social Min</div>
        <input type="number" step="1000" min="0" placeholder="R$ 0" value="<?= htmlspecialchars($capMin) ?>" onchange="navigateFilter('cap_min', this.value)">
    </div>
    <div class="filter-bar" style="flex:1;min-width:160px;">
        <div class="label"><i class="ph-bold ph-currency-circle-dollar"></i> Capital Social Max</div>
        <input type="number" step="1000" min="0" placeholder="R$ <?= number_format($capMaxVal, 0, ',', '.') ?>" value="<?= htmlspecialchars($capMax) ?>" onchange="navigateFilter('cap_max', this.value)">
    </div>
</div>

<script>
function navigateFilter(key, val) {
    var url = new URL(window.location.href, window.location.origin);
    if (val) url.searchParams.set(key, val);
    else url.searchParams.delete(key);
    window.location.href = url.toString();
}
</script>

<div class="bairro-stats" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
    <?php foreach ($bairrosList as $b):
        $active = $bairroFiltro === $b['bairro'];
        $badgeUrl = $active ? $qfUrl : $qfUrl . '&bairro=' . urlencode($b['bairro']) . ($filtro !== 'todos' ? '&f=' . $filtro : '') . ($capMin !== '' ? '&cap_min=' . $capMin : '') . ($capMax !== '' ? '&cap_max=' . $capMax : '');
    ?>
    <a href="<?= $badgeUrl ?>" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:500;text-decoration:none;background:<?= $active ? 'var(--gold)' : 'rgba(255,255,255,0.04)' ?>;color:<?= $active ? '#000' : 'var(--text-dim)' ?>;border:1px solid <?= $active ? 'var(--gold)' : 'var(--border)' ?>;transition:all .2s;">
        <?= htmlspecialchars($b['bairro']) ?>
        <span style="font-weight:700;"><?= number_format($b['total'], 0, ',', '.') ?></span>
    </a>
    <?php endforeach; ?>
</div>

<div class="table-wrap">
    <?php if ($results): $total = $total ?: count($results); $totalPages = ceil($total / $limit); ?>
    <div class="stats-line">
        <span><i class="ph-bold ph-database" style="font-size:13px;"></i> <?= number_format($total, 0, ',', '.') ?> registros encontrados<?= $cidadeNome ? " em $cidadeNome" : ' em MS' ?></span>
        <a href="?page=exportar&seg=<?= $segTable ?><?= $cidadeFiltro ? '&cidade=' . urlencode($cidadeFiltro) : '' ?><?= $bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '' ?>" class="btn-export"><i class="ph-bold ph-download-simple"></i> Exportar CSV</a>
    </div>
    <table>
        <thead><tr>
            <th>Razao Social</th><th>Fantasia</th><th>Bairro</th><th>Cidade</th><th>Telefone</th><th>Capital</th><th>Status</th>
        </tr></thead>
        <tbody>
            <?php foreach ($results as $r): ?>
            <tr>
                <td><strong><?= htmlspecialchars($r['razao_social'] ?? $r['nome_fantasia'] ?? 'N/I') ?></strong></td>
                <td><?= htmlspecialchars($r['nome_fantasia'] ?? '-') ?></td>
                <td>
                    <?php if ($r['bairro']): ?>
                    <a href="<?= $qfUrl ?>&bairro=<?= urlencode($r['bairro']) ?>" style="color:var(--text-dim);text-decoration:none;border-bottom:1px dashed var(--border);"><?= htmlspecialchars($r['bairro']) ?></a>
                    <?php else: ?>-<?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['nome_municipio'] ?? '-') ?></td>
                <td><?php
                    $telFmt = fmtTel($r['ddd'] ?? '', $r['telefone'] ?? '');
                    $telNum = preg_replace('/\D/', '', ($r['ddd'] ?? '') . ($r['telefone'] ?? ''));
                    echo $telFmt;
                    if ($telNum): ?> <a href="https://wa.me/55<?= $telNum ?>" target="_blank" title="WhatsApp" style="color:#25D366;text-decoration:none;font-size:14px;margin-left:4px;"><i class="ph-bold ph-whatsapp-logo"></i></a><?php endif;
                ?></td>
                <td style="font-family:var(--font-heading);font-weight:600;font-size:13px;"><?= ($r['capital_num'] ?? 0) > 0 ? 'R$ ' . number_format($r['capital_num'], 2, ',', '.') : '-' ?></td>
                <td><?= sitBadge($r['situacao_cadastral'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php
        $pageUrl = "?page=segment&seg=$segTable&q=" . urlencode($search) . "&f=$filtro" . ($bairroFiltro ? '&bairro=' . urlencode($bairroFiltro) : '') . ($capMin !== '' ? '&cap_min=' . $capMin : '') . ($capMax !== '' ? '&cap_max=' . $capMax : '') . ($cidadeFiltro ? '&cidade=' . urlencode($cidadeFiltro) : '');
        if ($page > 1): ?><a href="<?= $pageUrl ?>&p=<?= $page - 1 ?>"><i class="ph-bold ph-caret-left"></i> Anterior</a><?php endif;
        for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++):
            echo $i === $page ? '<span class="active">' . $i . '</span>' : '<a href="' . $pageUrl . '&p=' . $i . '">' . $i . '</a>';
        endfor;
        if ($page < $totalPages): ?><a href="<?= $pageUrl ?>&p=<?= $page + 1 ?>">Próxima <i class="ph-bold ph-caret-right"></i></a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php elseif ($search): ?>
    <div class="empty-state"><h3><i class="ph-bold ph-magnifying-glass"></i> Nenhum resultado para "<?= htmlspecialchars($search) ?>"</h3></div>
    <?php else: ?>
    <div class="empty-state">
        <h3><i class="ph-bold ph-buildings" style="font-size:28px;display:block;margin-bottom:8px;"></i> <?= $currentSeg['label'] ?></h3>
        <p><?= number_format($stats['total'], 0, ',', '.') ?> registros<?= $cidadeNome ? " em $cidadeNome" : ' em MS' ?>. Selecione um filtro ao lado ou use a busca acima.</p>
    </div>
    <?php endif; ?>
</div>

</section>
