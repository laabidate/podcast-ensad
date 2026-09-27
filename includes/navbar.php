<?php
/**
 * Navigation Bar - Micro ENSAD Mohammedia
 * Design Sombre Cinématique Haut de Gamme
 * Identité ENSAD, Navigation Active, Recherche Instantanée & CTA Écouter
 */
?>
<!-- ===== BARRE D'EN-TÊTE MOBILE OFFICIELLE (DEUX LOGOS : GAUCHE & DROITE) ===== -->
<header class="mobile-top-brand-bar d-md-none" id="mobileTopBrandBar" aria-label="Identité Micro ENSAD">
    <div class="mobile-top-brand-inner">
        <!-- 1. Logo Principal (Gauche) : ENSAD Casablanca -->
        <a href="index.php" class="mobile-brand-link mobile-brand-left" title="ENSAD Casablanca">
            <?php if (file_exists(__DIR__ . '/../assets/images/logo-principal.svg')): ?>
                <img src="assets/images/logo-principal.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-principal.svg') ?>" alt="Logo Officiel ENSAD" class="mobile-brand-logo mobile-logo-principal">
            <?php else: ?>
                <img src="assets/images/ensad-logo-white.png" alt="Logo Officiel ENSAD" class="mobile-brand-logo mobile-logo-principal d-theme-dark-only">
                <img src="assets/images/ensad-logo-navy.png" alt="Logo Officiel ENSAD" class="mobile-brand-logo mobile-logo-principal d-theme-light-only">
            <?php endif; ?>
        </a>


        <!-- 3. Logo Secondaire (Droite) : Université Hassan II Casablanca -->
        <a href="index.php" class="mobile-brand-link mobile-brand-right" title="Université Hassan II de Casablanca">
            <div class="mobile-logo-secondaire-box">
                <?php if (file_exists(__DIR__ . '/../assets/images/logo-secondaire.svg')): ?>
                    <img src="assets/images/logo-secondaire.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-secondaire.svg') ?>" alt="Université Hassan II Casablanca" class="mobile-brand-logo mobile-logo-secondaire">
                <?php else: ?>
                    <span class="small fw-bold font-heading text-white" style="font-size:0.7rem;">UH2C</span>
                <?php endif; ?>
            </div>
        </a>
    </div>
</header>

<nav class="navbar navbar-expand-md navbar-cinematic-top sticky-top" id="siteNavbar">
    <div class="container-xl">

        <!-- 1. IDENTITÉ : DEUX LOGOS OFFICIELS (Gauche) -->
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="d-flex align-items-center gap-3 text-decoration-none" title="ENSAD Mohammedia • Université Hassan II">
                <!-- Logo Principal : ENSAD -->
                <?php if (file_exists(__DIR__ . '/../assets/images/logo-principal.svg')): ?>
                    <img src="assets/images/logo-principal.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-principal.svg') ?>" alt="Logo ENSAD" class="nav-brand-ensad-logo">
                <?php else: ?>
                    <img src="assets/images/ensad-logo-white.png" alt="Logo Officiel ENSAD" class="nav-brand-ensad-logo d-theme-dark-only">
                    <img src="assets/images/ensad-logo-navy.png" alt="Logo Officiel ENSAD" class="nav-brand-ensad-logo d-theme-light-only">
                <?php endif; ?>

                <!-- Séparateur fin -->
                <span class="nav-brand-divider" aria-hidden="true"></span>

                <!-- Logo Secondaire : Université Hassan II -->
                <?php if (file_exists(__DIR__ . '/../assets/images/logo-secondaire.svg')): ?>
                    <img src="assets/images/logo-secondaire.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-secondaire.svg') ?>" alt="Université Hassan II Casablanca" class="nav-brand-logo-secondaire">
                <?php endif; ?>
            </a>
        </div>

        <!-- 2. LIENS DE NAVIGATION CENTRÉS (Desktop) -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3 text-center py-3 py-lg-0">
                <li class="nav-item">
                    <a class="nav-link-cinematic <?= ($activeNav === 'home') ? 'active' : '' ?>" href="index.php">
                        <span>Accueil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-cinematic <?= ($activeNav === 'episodes') ? 'active' : '' ?>" href="episodes.php">
                        <span>Épisodes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-cinematic <?= ($activeNav === 'podcasts') ? 'active' : '' ?>" href="podcasts.php">
                        <span>Podcasts</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-cinematic <?= ($activeNav === 'categories') ? 'active' : '' ?>" href="index.php#thematiques-section">
                        <span>Catégories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-cinematic <?= ($activeNav === 'about') ? 'active' : '' ?>" href="about.php">
                        <span>À propos</span>
                    </a>
                </li>
            </ul>

            <!-- 4. BOUTON RECHERCHE + THÈME + CTA ÉCOUTER (Droite) -->
            <div class="d-flex align-items-center justify-content-center gap-2 pt-2 pt-lg-0">
                <!-- Bouton Bascule Thème Clair/Sombre (Desktop) -->
                <button type="button" class="btn-nav-icon d-none d-lg-inline-flex" id="themeToggleBtn" title="Basculer Mode Clair / Sombre" aria-label="Changer de thème">
                    <span class="material-symbols-rounded" id="themeToggleIcon">light_mode</span>
                </button>

                <!-- Bouton Recherche (Ouvre la modale) -->
                <button type="button" class="btn-nav-icon d-none d-lg-inline-flex" data-bs-toggle="modal" data-bs-target="#globalSearchModal" title="Rechercher un épisode" aria-label="Rechercher">
                    <span class="material-symbols-rounded">search</span>
                </button>

                <!-- Bouton CTA Écouter (Bleu Électrique) -->
                <a href="#featured-section" class="btn-cta-electric" id="navListenBtn">
                    <span class="material-symbols-rounded" style="font-size: 1.15rem;">play_arrow</span>
                    <span>Écouter</span>
                </a>
            </div>
        </div>

    </div>
</nav>
