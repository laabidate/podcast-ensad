<?php
/**
 * Barre de Navigation Mobile Style Application Native (iOS / Android)
 * Micro ENSAD Mohammedia
 * 
 * S'affiche UNIQUEMENT sur mobile (< 768px).
 * Propose une expérience d'application native avec effet de verre (Glassmorphism),
 * 5 onglets clés, indicateur actif lumineux et micro-interactions tactiles.
 */
if (!isset($activeNav)) {
    $activeNav = 'home';
}
?>

<!-- ═════════════════════════════════════════════════════════════════════════ -->
<!-- BARRE DE NAVIGATION MOBILE TYPE APPLICATION (Android / iOS Style)          -->
<!-- ═════════════════════════════════════════════════════════════════════════ -->
<nav class="mobile-app-bottom-bar d-md-none" id="mobileAppBottomBar" role="navigation" aria-label="Navigation principale de l'application">
    <div class="mobile-app-bar-inner d-flex align-items-center justify-content-around">

        <!-- 1. Accueil -->
        <a href="index.php" class="mobile-app-tab <?= ($activeNav === 'home') ? 'active' : '' ?>" id="tabMobileHome" aria-label="Accueil">
            <div class="mobile-tab-icon-wrap">
                <span class="material-symbols-rounded mobile-tab-icon">home</span>
                <span class="mobile-tab-glow"></span>
            </div>
            <span class="mobile-tab-label">Accueil</span>
        </a>

        <!-- 2. Épisodes -->
        <a href="episodes.php" class="mobile-app-tab <?= ($activeNav === 'episodes' || $activeNav === 'podcasts') ? 'active' : '' ?>" id="tabMobileEpisodes" aria-label="Épisodes">
            <div class="mobile-tab-icon-wrap">
                <span class="material-symbols-rounded mobile-tab-icon">podcasts</span>
                <span class="mobile-tab-glow"></span>
            </div>
            <span class="mobile-tab-label">Épisodes</span>
        </a>

        <!-- 3. Recherche (Action Rapide - Ouvre la Modale Globale) -->
        <button type="button" class="mobile-app-tab btn-reset" id="tabMobileSearch" data-bs-toggle="modal" data-bs-target="#globalSearchModal" aria-label="Rechercher">
            <div class="mobile-tab-icon-wrap mobile-tab-search-bubble">
                <span class="material-symbols-rounded mobile-tab-icon">search</span>
            </div>
            <span class="mobile-tab-label">Recherche</span>
        </button>

        <!-- 4. Catégories / Thématiques -->
        <a href="index.php#thematiques-section" class="mobile-app-tab <?= ($activeNav === 'categories') ? 'active' : '' ?>" id="tabMobileCategories" aria-label="Catégories">
            <div class="mobile-tab-icon-wrap">
                <span class="material-symbols-rounded mobile-tab-icon">grid_view</span>
                <span class="mobile-tab-glow"></span>
            </div>
            <span class="mobile-tab-label">Catégories</span>
        </a>

        <!-- 5. Thème (Bascule Mode Clair / Sombre en bas) -->
        <button type="button" class="mobile-app-tab btn-reset" id="tabMobileThemeToggle" aria-label="Basculer Thème Clair / Sombre">
            <div class="mobile-tab-icon-wrap">
                <span class="material-symbols-rounded mobile-tab-icon" id="mobileTabThemeIcon">light_mode</span>
                <span class="mobile-tab-glow"></span>
            </div>
            <span class="mobile-tab-label" id="mobileTabThemeLabel">Thème</span>
        </button>

    </div>
</nav>
