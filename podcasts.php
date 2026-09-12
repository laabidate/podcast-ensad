<?php
/**
 * Liste des Émissions & Séries - Micro ENSAD Mohammedia
 * Dual Mode UX/UI avec Icônes Google Material Symbols et Boîtes Carrées 1:1
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Les Podcasts — Micro ENSAD";
$pageDescription = "Découvrez tous les podcasts étudiants de l'ENSAD Mohammedia. Arts visuels, design graphique, cinéma et performance.";
$pageImage = 'https://micro-ensad.ma/assets/images/hero-mic-cinematic.jpg';
$pageUrl = 'https://micro-ensad.ma/podcasts.php';
$activeNav = 'podcasts';

$selectedCategory = $_GET['cat'] ?? 'all';
$podcasts = getAllPodcasts($selectedCategory !== 'all' ? $selectedCategory : null);
$categories = getCategories();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4 py-lg-5">

    <!-- En-tête -->
    <div class="text-center max-w-2xl mx-auto mb-5">
        <div class="badge-ep-tag mb-2">Séries Officielles • ENSAD Mohammedia</div>
        <h1 class="display-5 font-fredoka fw-bold mb-2" style="color: var(--text-main);">Les Séries de l'ENSAD</h1>
        <p class="text-muted mx-auto" style="max-width: 680px; line-height: 1.75;">
            Explorez les 11 séries audio créées par les étudiants du cycle Bac+5 (DGI &amp; GDA) sous l'encadrement de Madame Randa El Amraoui.
        </p>
    </div>

    <!-- Filtres par catégorie -->
    <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
        <a href="podcasts.php?cat=all" class="pill-category-light <?= ($selectedCategory === 'all') ? 'active' : '' ?>">
            <span class="material-symbols-rounded" style="font-size: 1rem;">grid_view</span>
            <span>Toutes les filières</span>
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="podcasts.php?cat=<?= urlencode($cat) ?>" class="pill-category-light <?= ($selectedCategory === $cat) ? 'active' : '' ?>">
                <?= e($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Grille des Émissions (Boîtes Carrées 1:1) -->
    <div class="row g-4">
        <?php foreach ($podcasts as $pod): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-podcast-light h-100 d-flex flex-column">
                    <!-- Boîte carrée 1:1 -->
                    <div class="card-podcast-cover-wrap">
                        <img src="<?= e($pod['cover']) ?>" alt="<?= e($pod['title']) ?>" class="card-podcast-img" loading="lazy">
                        <span class="badge position-absolute top-0 start-0 m-3 px-3 py-1 rounded-pill font-fredoka" style="background: var(--ensad-blue); font-size: 0.72rem; color: #fff;">
                            <?= e($pod['category']) ?>
                        </span>
                        <span class="badge position-absolute top-0 end-0 m-3 px-2 py-1 rounded-pill font-fredoka bg-white text-dark border shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                            <span class="material-symbols-rounded" style="font-size: 0.85rem;">mic</span>
                            <span><?= $pod['episodes_count'] ?> épisode</span>
                        </span>
                    </div>

                    <div class="card-podcast-body d-flex flex-column flex-grow-1">
                        <div class="text-muted small mb-1 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                            <span class="material-symbols-rounded text-primary" style="font-size: 0.95rem;">school</span>
                            <span><?= e($pod['department']) ?></span>
                        </div>

                        <h3 class="card-podcast-title mb-2">
                            <a href="podcast-detail.php?id=<?= $pod['id'] ?>">
                                <?= e($pod['title']) ?>
                            </a>
                        </h3>

                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.6; font-size: 0.8rem;">
                            <?= e($pod['description']) ?>
                        </p>

                        <div class="pt-2 mb-3">
                            <div class="text-muted" style="font-size: 0.7rem;">👥 Équipe étudiante :</div>
                            <div class="small fw-semibold text-truncate" style="color: var(--text-main);"><?= e($pod['hosts']) ?></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 mt-auto border-top" style="border-color: var(--border-color)!important;">
                            <a href="podcast-detail.php?id=<?= $pod['id'] ?>" class="btn btn-sm btn-ensad-primary rounded-pill px-3 w-100 justify-content-center">
                                <span>Voir l'épisode</span>
                                <span class="material-symbols-rounded" style="font-size: 1rem;">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
