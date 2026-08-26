<?php
$pageAtual = $page ?? 'dashboard';
function tabUrl($tabName) {
    $url = "?page=$tabName";
    global $cidadeFiltro;
    if ($cidadeFiltro) $url .= '&cidade=' . urlencode($cidadeFiltro);
    return $url;
}
function segUrl($seg) {
    $url = "?page=segment&seg=$seg";
    global $cidadeFiltro;
    if ($cidadeFiltro) $url .= '&cidade=' . urlencode($cidadeFiltro);
    return $url;
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CRM Parceiros — LC Soluções em Móveis</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/bold/style.css">
<link rel="stylesheet" href="assets/css/crm.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<nav class="nav">
<div class="nav-inner" id="mainNav">
    <div class="nav-brand">LC <span>CRM</span></div>
    <div class="nav-city">
        <select id="navCidade" onchange="navegarCidade()">
            <option value="">MS - Todas as cidades</option>
            <?php foreach ($cidades as $cod => $nome):
                $sel = ($cidadeFiltro ?? '') === $cod ? 'selected' : '';
            ?>
            <option value="<?= $cod ?>" <?= $sel ?>><?= htmlspecialchars($nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="nav-divider"></div>
    <a class="nav-item <?= $pageAtual === 'dashboard' ? 'active' : '' ?>" href="<?= tabUrl('dashboard') ?>">
        <i class="ph-bold ph-squares-four"></i>
        Dashboard
    </a>
    <a class="nav-item <?= $pageAtual === 'funil' ? 'active' : '' ?>" href="<?= tabUrl('funil') ?>">
        <i class="ph-bold ph-funnel"></i>
        Funil
    </a>
    <a class="nav-item <?= $pageAtual === 'clientes' ? 'active' : '' ?>" href="<?= tabUrl('clientes') ?>">
        <i class="ph-bold ph-users-three"></i>
        Clientes
    </a>
    <a class="nav-item <?= $pageAtual === 'projetos' ? 'active' : '' ?>" href="<?= tabUrl('projetos') ?>">
        <i class="ph-bold ph-clipboard-text"></i>
        Projetos
    </a>
    <div class="nav-divider"></div>
    <a class="nav-item <?= $pageAtual === 'exportar' ? 'active' : '' ?>" href="<?= tabUrl('exportar') ?>">
        <i class="ph-bold ph-download"></i>
        Exportar
    </a>
    <a class="nav-item <?= $pageAtual === 'email' ? 'active' : '' ?>" href="<?= tabUrl('email') ?>">
        <i class="ph-bold ph-envelope"></i>
        Email
    </a>
    <a class="nav-item <?= $pageAtual === 'tracking' ? 'active' : '' ?>" href="<?= tabUrl('tracking') ?>">
        <i class="ph-bold ph-trend-up"></i>
        Tracking
    </a>
    <a class="nav-item <?= $pageAtual === 'whatsapp' ? 'active' : '' ?>" href="<?= tabUrl('whatsapp') ?>">
        <i class="ph-bold ph-whatsapp-logo"></i>
        WhatsApp
    </a>
    <div class="nav-divider"></div>
    <a class="nav-item <?= in_array($pageAtual, ['segmentos', 'segment']) ? 'active' : '' ?>" href="?page=segmentos">
        <i class="ph-bold ph-stack-simple"></i>
        Segmentos
    </a>
    <a class="nav-item <?= $pageAtual === 'charts' ? 'active' : '' ?>" href="<?= tabUrl('charts') ?>">
        <i class="ph-bold ph-chart-bar"></i>
        Graficos
    </a>
    <div class="nav-divider"></div>
    <a class="nav-item <?= $pageAtual === 'chat' ? 'active' : '' ?>" href="<?= tabUrl('chat') ?>">
        <i class="ph-bold ph-chat-circle-dots"></i>
        Chat IA
    </a>
    <div style="margin-left:auto;display:flex;align-items:center;gap:8px;padding:0 10px;">
        <a href="?page=perfil" class="nav-user" style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text);text-decoration:none;padding:5px 10px;border-radius:8px;transition:background .2s;" onmouseover="this.style.background='rgba(197,162,83,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="ph-bold ph-user"></i>
            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>
        </a>
        <a href="logout.php" style="font-size:11px;color:var(--gold);text-decoration:none;font-weight:600;">Sair</a>
    </div>
</div>
</nav>
<script src="assets/js/crm.js"></script>
<script>
if (typeof Chart !== 'undefined') {
    Chart.defaults.color = '#999';
    Chart.defaults.borderColor = 'rgba(197,162,83,0.08)';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyle = 'circle';
    Chart.defaults.elements.bar.borderRadius = 4;
}
</script>
<main class="container">
