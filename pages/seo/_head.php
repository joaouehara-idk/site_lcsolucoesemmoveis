<?php
$seo_title ??= 'LC Soluções em Móveis';
$seo_description ??= 'Móveis planejados em Campo Grande com design exclusivo e qualidade premium.';
$seo_keywords ??= 'móveis planejados, marcenaria, Campo Grande';
$seo_url ??= SITE_URL . '/seo';
$seo_image ??= SITE_URL . '/assets/img/logo.jpg';
$schemas ??= [];
$canonical_url = $seo_url;
$page_slug ??= '';
$base_seo_url = SITE_URL . '/seo';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($seo_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo_description) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seo_keywords) ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="LC Soluções em Móveis">
    <link rel="canonical" href="<?= $canonical_url ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= htmlspecialchars($seo_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo_description) ?>">
    <meta property="og:image" content="<?= $seo_image ?>">
    <meta property="og:url" content="<?= $canonical_url ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo_description) ?>">
    <meta name="twitter:image" content="<?= $seo_image ?>">

    <!-- FAQ Schema (if applicable) -->
    <?php if (!empty($schemas)): ?>
    <script type="application/ld+json">
    <?= json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php endif; ?>

    <!-- BreadcrumbList Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://lcsolucoesemmoveis.com.br"},
            {"@type": "ListItem", "position": 2, "name": "<?= htmlspecialchars($seo_title) ?>", "item": "<?= $canonical_url ?>"}
        ]
    }
    </script>

    <!-- Schema LocalBusiness (main site) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "https://lcsolucoesemmoveis.com.br/#localbusiness",
        "name": "LC Soluções em Móveis",
        "image": "<?= SITE_URL ?>/assets/img/logo.jpg",
        "telephone": "(67) 3253-7898",
        "email": "lcmovel.planejadocg@gmail.com",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Rua Francisco José Abrão, 525",
            "addressLocality": "Campo Grande",
            "addressRegion": "MS",
            "postalCode": "79011-410",
            "addressCountry": "BR"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": -20.4697,
            "longitude": -54.6201
        },
        "url": "https://lcsolucoesemmoveis.com.br",
        "sameAs": [
            "https://www.instagram.com/lcsolucoesemmoveis",
            "https://wa.me/556732537898",
            "https://share.google/H49kTxA1e770jFlJJ"
        ],
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "5.0",
            "reviewCount": "16"
        },
        "areaServed": "Campo Grande e Região",
        "priceRange": "$$",
        "openingHours": "Mo-Fr 08:00-18:00, Sa 08:00-12:00"
    }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#0a0a0a;color:#e0d5c8;line-height:1.8}
        .seo-container{max-width:820px;margin:0 auto;padding:40px 24px}
        h1{font-family:'Playfair Display',serif;font-size:2.6rem;font-weight:900;color:#ffffff;margin-bottom:20px;line-height:1.2}
        h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:800;color:#ffffff;margin:40px 0 16px}
        h3{font-size:1.3rem;font-weight:600;color:#ffffff;margin:30px 0 12px}
        p{font-size:1.05rem;margin-bottom:18px;color:#c5b9ab}
        ul,ol{margin:0 0 20px 24px;color:#c5b9ab}
        li{margin-bottom:8px;font-size:1.02rem}
        a{color:#ffffff;text-decoration:none;transition:color .3s}
        a:hover{color:#cccccc;text-decoration:underline}
        .seo-header{text-align:center;padding:60px 0 30px;border-bottom:1px solid rgba(212,168,83,0.2);margin-bottom:40px}
        .seo-header .subtitle{font-size:1.1rem;color:#a09080;margin-top:8px}
        .seo-intro{font-size:1.15rem;color:#d4c5b5;border-left:3px solid #ffffff;padding-left:20px;margin:30px 0}
        .seo-cta{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);border-radius:16px;padding:40px;text-align:center;margin:50px 0}
        .seo-cta h2{color:#ffffff;margin-top:0}
        .seo-cta p{font-size:1.05rem;margin-bottom:24px}
        .seo-btn{display:inline-block;background:#ffffff;color:#0a0a0a;font-weight:700;padding:16px 40px;border-radius:12px;font-size:1.1rem;transition:all .3s;text-decoration:none}
        .seo-btn:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(255,255,255,0.2);text-decoration:none;color:#0a0a0a}
        .seo-btn i{margin-right:8px}
        .seo-footer{text-align:center;padding:40px 0;border-top:1px solid rgba(212,168,83,0.15);margin-top:60px;color:#7a7060;font-size:0.9rem}
        .seo-footer a{color:#a09080}
        .seo-internallinks{background:rgba(255,255,255,0.03);border-radius:12px;padding:24px;margin:40px 0}
        .seo-internallinks h3{margin-top:0;font-size:1.1rem}
        .seo-internallinks ul{list-style:none;margin:12px 0 0;padding:0;display:flex;flex-wrap:wrap;gap:8px}
        .seo-internallinks ul li{margin:0}
        .seo-internallinks ul li a{display:inline-block;background:rgba(255,255,255,0.06);padding:6px 16px;border-radius:20px;font-size:0.85rem;color:#c5b9ab;transition:all .3s}
        .seo-internallinks ul li a:hover{background:rgba(255,255,255,0.12);color:#ffffff;text-decoration:none}
        .highlight-box{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:24px;margin:30px 0}
        .faq-item{border-bottom:1px solid rgba(255,255,255,0.06);padding:20px 0}
        .faq-item:last-child{border-bottom:none}
        .faq-item h3{font-family:'Inter',sans-serif;font-size:1.05rem;color:#ffffff;margin:0 0 8px}
        .faq-item p{margin:0;font-size:0.98rem;color:#b0a090}
        .seo-proscons{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin:30px 0}
        .seo-proscons .pros{background:rgba(76,175,80,0.06);border:1px solid rgba(76,175,80,0.15);border-radius:12px;padding:20px}
        .seo-proscons .cons{background:rgba(244,67,54,0.06);border:1px solid rgba(244,67,54,0.15);border-radius:12px;padding:20px}
        .seo-proscons h4{font-size:1.05rem;margin-bottom:12px}
        .seo-proscons ul{list-style:none;padding:0;margin:0}
        .seo-proscons ul li{padding:4px 0;font-size:0.95rem}
        table{width:100%;border-collapse:collapse;margin:30px 0;font-size:0.95rem}
        th,td{padding:12px 16px;text-align:left;border-bottom:1px solid rgba(255,255,255,0.06)}
        th{background:rgba(255,255,255,0.06);color:#ffffff;font-weight:600}
        td{color:#c5b9ab}
        .seo-toc{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:24px;margin:30px 0}
        .seo-toc h3{margin-top:0;margin-bottom:12px;font-size:1rem;text-transform:uppercase;letter-spacing:1px}
        .seo-toc ol{margin:0;padding-left:20px}
        .seo-toc ol li{margin-bottom:6px;font-size:0.95rem}
        .seo-toc ol li a{color:#c5b9ab}
        .seo-toc ol li a:hover{color:#ffffff}
        @media(max-width:600px){
            h1{font-size:1.8rem}
            h2{font-size:1.4rem}
            .seo-proscons{grid-template-columns:1fr}
            .seo-container{padding:24px 16px}
            .seo-cta{padding:24px 16px}
            table{font-size:0.85rem}
            th,td{padding:8px 10px}
        }
    </style>
</head>
<body>
    <div class="seo-container">
        <header class="seo-header">
            <h1><?= htmlspecialchars($seo_heading ?? $seo_title) ?></h1>
            <p class="subtitle"><?= htmlspecialchars($seo_subtitle ?? '') ?></p>
        </header>
