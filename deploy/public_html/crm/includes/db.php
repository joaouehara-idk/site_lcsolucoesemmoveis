<?php
require_once __DIR__ . '/db_config.php';
$conn = getDbConnection();
$conn->set_charset('utf8mb4');

$segments = [
    'arquitetos_ms'           => ['label' => 'Arquitetos',              'icon' => 'arquitetura', 'color' => '#1a1a2e'],
    'designers_interiores_ms' => ['label' => 'Designers Interiores',    'icon' => 'design',      'color' => '#e94560'],
    'designers_ms'            => ['label' => 'Designers',               'icon' => 'design',      'color' => '#0f3460'],
    'construtoras_ms'         => ['label' => 'Construtoras',            'icon' => 'construcao',  'color' => '#16213e'],
    'imobiliarias_ms'         => ['label' => 'Imobiliárias',            'icon' => 'imovel',      'color' => '#533483'],
    'engenheiros_ms'          => ['label' => 'Engenheiros',             'icon' => 'engenharia',  'color' => '#1a5276'],
    'lojas_marcenarias_ms'    => ['label' => 'Lojas e Marcenarias',     'icon' => 'marcenaria',  'color' => '#7d6608'],
    'paisagismo_decoracao_ms' => ['label' => 'Paisagismo e Decoração',  'icon' => 'paisagismo',  'color' => '#1e8449'],
];

$cidades = [];
$r = $conn->query("SELECT codigo, nome FROM municipios WHERE uf = 'MS' ORDER BY nome");
while ($row = $r->fetch_assoc()) {
    $cidades[$row['codigo']] = $row['nome'];
}

$sitLabels = ['01' => 'Nula', '02' => 'Ativa', '03' => 'Suspensa', '04' => 'Inapta', '08' => 'Baixada'];

function fmtTel($ddd, $tel) {
    if (!$tel) return '-';
    $tel = preg_replace('/\D/', '', $tel);
    $ddd = preg_replace('/\D/', '', $ddd);
    if (!$ddd) return $tel;
    if (strlen($tel) === 8) return "($ddd) " . substr($tel, 0, 4) . '-' . substr($tel, 4);
    if (strlen($tel) >= 9) return "($ddd) " . substr($tel, 0, 5) . '-' . substr($tel, 5);
    return "($ddd) $tel";
}

function sitBadge($cod) {
    $map = ['02' => ['label' => 'Ativa', 'class' => 'badge-ativo'], '08' => ['label' => 'Baixada', 'class' => 'badge-baixada']];
    $item = $map[$cod] ?? ['label' => $cod ?: 'N/I', 'class' => 'badge-outro'];
    return '<span class="badge ' . $item['class'] . '">' . $item['label'] . '</span>';
}

$icons = [
    'arquitetura' => '<i class="ph-bold ph-building"></i>',
    'design' => '<i class="ph-bold ph-compass-tool"></i>',
    'construcao' => '<i class="ph-bold ph-triangle"></i>',
    'imovel' => '<i class="ph-bold ph-house-line"></i>',
    'engenharia' => '<i class="ph-bold ph-gear-six"></i>',
    'marcenaria' => '<i class="ph-bold ph-ruler"></i>',
    'paisagismo' => '<i class="ph-bold ph-leaf"></i>',
];

