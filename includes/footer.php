<?php
/**
 * Footer Global - Micro ENSAD Mohammedia
 * Dual Logo, Thème Clair/Sombre, et Icônes Google Material Symbols.
 * Lit data/site-settings.json pour les réseaux sociaux et textes du footer.
 */

// Load site settings
$_siteSettings = [];
$_siteSettingsFile = __DIR__ . '/../data/site-settings.json';
if (file_exists($_siteSettingsFile)) {
    $_raw = file_get_contents($_siteSettingsFile);
    $_siteSettings = json_decode($_raw, true) ?? [];
}
$_footerData  = $_siteSettings['footer']  ?? [];
$_socialData  = $_siteSettings['social']  ?? [];
$_navbarData  = $_siteSettings['navbar']  ?? [];

// Social icon mapping (Bootstrap Icons)
$_socialIcons = [
    'instagram' => 'bi-instagram',
    'youtube'   => 'bi-youtube',
    'linkedin'  => 'bi-linkedin',
    'spotify'   => 'bi-spotify',
    'facebook'  => 'bi-facebook',
    'tiktok'    => 'bi-tiktok',
];
?>
</main>

<!-- 10. FOOTER CINÉMATIQUE ÉPURÉ & ÉDITORIAL -->
<footer class="site-cinematic-footer pt-5 pb-4 mt-5">
    <div class="container-xl">
        <div class="row g-4 align-items-center justify-content-between pb-4 border-bottom border-secondary border-opacity-10">
            
            <!-- 1. Identité Gauche -->
            <div class="col-12 col-md-4 col-lg-3">
                <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none mb-2" title="ENSAD Casablanca">
                    <?php if (file_exists(__DIR__ . '/../assets/images/logo-principal.svg')): ?>
                        <img src="assets/images/logo-principal.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-principal.svg') ?>" alt="Logo Officiel ENSAD" class="footer-ensad-logo">
                    <?php else: ?>
                        <img src="assets/images/ensad-logo-white.png" alt="Logo Officiel ENSAD" class="footer-ensad-logo d-theme-dark-only">
                        <img src="assets/images/ensad-logo-navy.png" alt="Logo Officiel ENSAD" class="footer-ensad-logo d-theme-light-only">
                    <?php endif; ?>
                    <div class="d-flex flex-column lh-sm ms-1">
                        <span class="font-heading fw-bold text-white small">Micro <span class="text-electric-blue">ENSAD</span></span>
                        <span class="text-muted" style="font-size: 0.7rem;"><?= e($_footerData['podcast_tagline'] ?? 'Le Podcast des Étudiants') ?></span>
                    </div>
                </a>
            </div>

            <!-- 2. Liens Centraux -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="d-flex flex-wrap gap-x-4 gap-y-2 justify-content-start justify-content-md-center small">
                    <a href="index.php" class="footer-nav-link me-3">Accueil</a>
                    <a href="episodes.php" class="footer-nav-link me-3">Épisodes</a>
                    <a href="index.php#thematiques-section" class="footer-nav-link me-3">Catégories</a>
                    <a href="about.php" class="footer-nav-link me-3">À propos</a>
                    <a href="welcome.php?preview=1" class="footer-nav-link" title="Revoir la page d'accueil de bienvenue"><i class="bi bi-play-btn me-1"></i>Bienvenue</a>
                </div>
            </div>

            <!-- 3. Réseaux Sociaux (dynamiques depuis site-settings.json) -->
            <div class="col-12 col-md-4 col-lg-2 text-start text-md-center">
                <div class="d-inline-flex align-items-center gap-2 flex-wrap">
                    <?php foreach ($_socialIcons as $_sKey => $_sIcon): ?>
                        <?php
                            $_sInfo = $_socialData[$_sKey] ?? [];
                            if (empty($_sInfo['enabled']) || empty($_sInfo['url'])) continue;
                        ?>
                        <a href="<?= e($_sInfo['url']) ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-social-circle"
                           title="<?= e($_sInfo['label'] ?? $_sKey) ?>"
                           aria-label="<?= e($_sInfo['label'] ?? $_sKey) ?>">
                            <i class="bi <?= e($_sIcon) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 4. Informations Institutionnelles Droite -->
            <div class="col-12 col-lg-4 text-start text-lg-end">
                <p class="text-muted small mb-1" style="font-size: 0.76rem; line-height: 1.5;">
                    <?= e($_footerData['institution_name'] ?? "École Nationale Supérieure d'Art et de Design de Casablanca") ?><br>
                    <?= e($_footerData['university_name'] ?? 'Université Hassan II de Casablanca') ?>
                </p>
                <p class="text-muted small mb-0" style="font-size: 0.72rem;">
                    &copy; <?= date('Y') ?> <?= e($_footerData['copyright_name'] ?? 'Micro ENSAD') ?>. Tous droits réservés.
                </p>
            </div>

        </div>
    </div>
</footer>


<!-- Modale de Recherche Globale Instantanée -->
<?php require_once __DIR__ . '/search-modal.php'; ?>

<!-- Modale de Détails d'Épisode (Chapitres, Contributeurs, Audio) -->
<?php require_once __DIR__ . '/episode-modal.php'; ?>

<!-- Modale de Partage & Carte QR Code Story (100% Statique) -->
<?php require_once __DIR__ . '/qr-share-modal.php'; ?>

<!-- Barre de Navigation Mobile Style Application Native (iOS / Android) -->
<?php require_once __DIR__ . '/mobile-app-bar.php'; ?>

<!-- Lecteur Audio YouTube Persistant avec Positionnement Interactif -->
<?php require_once __DIR__ . '/player.php'; ?>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- Moteur Autonome de Génération QR Code -->
<script src="assets/js/qrcode.min.js" defer></script>

<!-- Données Globales des Épisodes pour Recherche Instantanée & Partage -->
<script>
window.ENSAD_EPISODES = <?= json_encode(getEpisodes(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>

<!-- Scripts ENSAD -->
<script src="assets/js/player.js" defer></script>
<script src="assets/js/app.js" defer></script>

</body>
</html>
