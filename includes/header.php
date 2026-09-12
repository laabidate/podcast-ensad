<?php
/**
 * Header Global — Micro ENSAD
 * SEO-ready: Open Graph, Twitter Cards, Schema.org, Canonical, PWA
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Micro ENSAD • Le Podcast Officiel des Étudiants ENSAD Mohammedia';
}
if (!isset($activeNav)) {
    $activeNav = 'home';
}
if (!isset($pageDescription)) {
    $pageDescription = "Découvrez les réflexions, projets et créations des étudiants de l'École Nationale Supérieure d'Art et de Design de Mohammedia (Université Hassan II). Design, arts visuels, cinéma et innovations sonores.";
}
if (!isset($pageImage)) {
    $pageImage = 'https://micro-ensad.ma/assets/images/hero-mic-cinematic.jpg';
}
if (!isset($pageUrl)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $pageUrl = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'micro-ensad.ma') . ($_SERVER['REQUEST_URI'] ?? '/');
    $pageUrl = strtok($pageUrl, '?'); // Remove query strings from canonical
}
?>
<!DOCTYPE html>
<html lang="fr" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">

    <!-- ═══════════════════════════════ PRIMARY SEO ═══════════════════════════════ -->
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="podcast ENSAD, Micro ENSAD, étudiants Mohammedia, art et design Maroc, podcast art, ENSAD Casablanca, podcast étudiant, design graphique, cinéma Maroc">
    <meta name="author" content="Micro ENSAD — École Nationale Supérieure d'Art et de Design">
    <meta name="robots" content="index, follow">
    <meta name="language" content="French">
    <meta name="revisit-after" content="7 days">
    <link rel="canonical" href="<?= htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') ?>">

    <!-- ═══════════════════════════════ OPEN GRAPH ═══════════════════════════════ -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Micro ENSAD">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Micro ENSAD — Le Podcast des Étudiants">
    <meta property="og:url" content="<?= htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="fr_MA">

    <!-- ═══════════════════════════════ TWITTER CARDS ═════════════════════════════ -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image:alt" content="Micro ENSAD">

    <!-- ════════════════════════════ SCHEMA.ORG (JSON-LD) ════════════════════════ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Micro ENSAD",
        "alternateName": "Le Podcast des Étudiants ENSAD",
        "url": "https://micro-ensad.ma",
        "description": "Plateforme officielle des podcasts de l'École Nationale Supérieure d'Art et de Design de Mohammedia",
        "inLanguage": "fr",
        "publisher": {
            "@type": "EducationalOrganization",
            "name": "École Nationale Supérieure d'Art et de Design — Mohammedia",
            "url": "https://micro-ensad.ma",
            "parentOrganization": {
                "@type": "CollegeOrUniversity",
                "name": "Université Hassan II de Casablanca"
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "https://micro-ensad.ma/episodes.php?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- ═══════════════════════════════ PWA / MOBILE ═════════════════════════════ -->
    <meta name="theme-color" content="#050B14" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#F6F9FD" media="(prefers-color-scheme: light)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Micro ENSAD">
    <meta name="application-name" content="Micro ENSAD">
    <meta name="msapplication-TileColor" content="#1264FF">
    <link rel="manifest" href="manifest.json">

    <!-- ═══════════════════════════════ FAVICON ══════════════════════════════════ -->
    <link rel="icon" type="image/svg+xml" href="assets/images/micro-ensad-icon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/micro-ensad-icon.svg">
    <link rel="apple-touch-icon" href="assets/images/micro-ensad-icon.svg">

    <!-- ══════════════════════════════ PERFORMANCE ════════════════════════════════ -->
    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//www.youtube.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- ═══════════════════════════════ FONTS ════════════════════════════════════ -->
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Montserrat:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- ═══════════════════════════════ CSS ══════════════════════════════════════ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- ═══════════════════ THEME INIT (prevent flash) ═══════════════════════════ -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('ensad_theme') || 'dark';
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-theme', theme);
            } catch(e) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>
<?php require_once __DIR__ . '/navbar.php'; ?>
<main class="flex-grow-1">
