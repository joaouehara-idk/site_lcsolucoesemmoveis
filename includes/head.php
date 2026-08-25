<?php
$page_title = $page_title ?? $title ?? "LC Soluções em Móveis | Móveis Planejados Sob Medida em Campo Grande";
$page_description = $page_description ?? $description ?? "Especialistas em móveis planejados em MDF com design exclusivo e qualidade premium. Transforme sua casa ou empresa em Campo Grande/MS.";
$page_keywords = $page_keywords ?? "móveis planejados, marcenaria premium, cozinhas planejadas, quartos sob medida, Campo Grande MS";
$canonical_url = $canonical_url ?? (SITE_URL . $_SERVER['REQUEST_URI']);
$og_image = $og_image ?? SITE_URL . "/assets/img/logo.jpg";
$og_type = $og_type ?? 'website';
$breadcrumb_atual = $breadcrumb_atual ?? null;
$breadcrumb_url = $breadcrumb_url ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="LC Soluções em Móveis">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">
    <link rel="icon" href="<?php echo BASE_URL; ?>/assets/img/icon.png.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/img/icon.png.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/img/icon.png.ico">

    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo $og_type; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo $og_image; ?>">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo $og_image; ?>">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Serif+Display&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Analytics / Ads -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5253082939210672" crossorigin="anonymous"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17617194867"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-17617194867');
    </script>

    <!-- Schema.org: LocalBusiness -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "https://lcsolucoesemmoveis.com.br/#localbusiness",
        "name": "LC Soluções em Móveis",
        "description": "Marcenaria de móveis planejados sob medida em MDF em Campo Grande, MS.",
        "image": "<?php echo SITE_URL; ?>/assets/img/logo.jpg",
        "telephone": "+55 67 3253-7898",
        "email": "lcmovel.planejadocg@gmail.com",
        "url": "https://lcsolucoesemmoveis.com.br",
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
        "openingHours": "Mo-Fr 08:00-18:00, Sa 08:00-12:00",
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
        "areaServed": ["Campo Grande", "Mato Grosso do Sul"],
        "priceRange": "$$"
    }
    </script>

    <!-- Schema.org: BreadcrumbList -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Início",
                "item": "<?php echo SITE_URL; ?>/"
            }<?php if ($breadcrumb_atual && $breadcrumb_url): ?>,
            {
                "@type": "ListItem",
                "position": 2,
                "name": "<?php echo htmlspecialchars($breadcrumb_atual); ?>",
                "item": "<?php echo $breadcrumb_url; ?>"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "<?php echo htmlspecialchars($page_title); ?>",
                "item": "<?php echo $canonical_url; ?>"
            }<?php else: ?>,
            {
                "@type": "ListItem",
                "position": 2,
                "name": "<?php echo htmlspecialchars($page_title); ?>",
                "item": "<?php echo $canonical_url; ?>"
            }<?php endif; ?>
        ]
    }
    </script>

    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=2.1">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>