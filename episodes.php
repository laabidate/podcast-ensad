<?php
/**
 * Catalogue Général des Épisodes - Micro ENSAD Casablanca
 * Design Sombre Cinématique Éditorial
 * Grille responsive 4 colonnes, filtres par thématiques, recherche et lecteur persistant.
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Tous les Épisodes — Micro ENSAD Podcast";
$pageDescription = "Parcourez tous les épisodes du podcast Micro ENSAD. Créations sonores, projets artistiques et réflexions des étudiants de l'ENSAD Mohammedia.";
$pageImage = 'https://micro-ensad.ma/assets/images/hero-mic-cinematic.jpg';
$pageUrl = 'https://micro-ensad.ma/episodes.php';
$activeNav = 'episodes';

$searchQuery = $_GET['q'] ?? null;
$selectedCategory = $_GET['cat'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'recent';

$episodes = getEpisodes(null, null, $selectedCategory !== 'all' ? $selectedCategory : null, $searchQuery);

// Tri
if ($sortBy === 'popular') {
    usort($episodes, fn($a, $b) => ($b['plays'] ?? 0) <=> ($a['plays'] ?? 0));
} elseif ($sortBy === 'duration') {
    usort($episodes, fn($a, $b) => ($b['duration_seconds'] ?? 0) <=> ($a['duration_seconds'] ?? 0));
}

$categories = getCategories();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4 py-lg-5">

    <!-- En-tête Éditorial -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 mb-4 pb-2 border-bottom border-secondary border-opacity-10">
        <div>
            <div class="hero-badge-official mb-2">
                <span class="material-symbols-rounded" style="font-size: 0.95rem;">podcasts</span>
                <span>CATALOGUE OFFICIEL • 11 ÉPISODES</span>
            </div>
            <h1 class="display-5 fw-bold text-white mb-2" style="letter-spacing: -0.02em;">Tous les Épisodes</h1>
            <p class="text-muted mb-0" style="max-width: 600px; font-size: 0.95rem;">
                Explorez l'intégralité des créations audio produites par les étudiants des filières Design Graphique &amp; Interactif et Game Design &amp; Animation.
            </p>
        </div>

        <!-- Recherche & Tri -->
        <form action="episodes.php" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <?php if ($selectedCategory !== 'all'): ?>
                <input type="hidden" name="cat" value="<?= e($selectedCategory) ?>">
            <?php endif; ?>

            <div class="position-relative" style="min-width: 250px;">
                <input type="text" name="q" value="<?= e($searchQuery) ?>" 
                       class="form-control form-control-sm search-input-field ps-4 pe-5" 
                       placeholder="Rechercher étudiant, sujet..." 
                       id="liveCatalogueSearch">
                <span class="material-symbols-rounded position-absolute end-0 top-50 translate-middle-y text-muted pe-3" style="font-size: 1.1rem; pointer-events: none;">search</span>
            </div>

            <select name="sort" class="form-select form-select-sm rounded-pill search-input-field" onchange="this.form.submit()" style="width: auto; padding-right: 2rem;">
                <option value="recent" <?= ($sortBy === 'recent') ? 'selected' : '' ?>>Ordre officiel</option>
                <option value="popular" <?= ($sortBy === 'popular') ? 'selected' : '' ?>>Plus écoutés</option>
                <option value="duration" <?= ($sortBy === 'duration') ? 'selected' : '' ?>>Plus longs</option>
            </select>
        </form>
    </div>

    <!-- Filtres par Thématique (Pilules Cinématiques) -->
    <div class="thematic-filter-container mb-4">
        <a href="episodes.php?cat=all<?= $searchQuery ? '&q=' . urlencode($searchQuery) : '' ?>" 
           class="filter-pill-cinematic <?= ($selectedCategory === 'all') ? 'active' : '' ?>">
            Tous les épisodes
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="episodes.php?cat=<?= urlencode($cat) ?><?= $searchQuery ? '&q=' . urlencode($searchQuery) : '' ?>" 
               class="filter-pill-cinematic <?= ($selectedCategory === $cat) ? 'active' : '' ?>">
                <?= e($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($searchQuery): ?>
        <div class="p-3 rounded-4 d-flex align-items-center justify-content-between mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
            <span class="small text-muted">Résultats pour la recherche : <strong class="text-white">"<?= e($searchQuery) ?>"</strong> (<?= count($episodes) ?> épisode<?= count($episodes) > 1 ? 's' : '' ?> trouvé<?= count($episodes) > 1 ? 's' : '' ?>)</span>
            <a href="episodes.php" class="btn btn-sm btn-outline-secondary rounded-pill py-0" style="font-size: 0.78rem;">Effacer</a>
        </div>
    <?php endif; ?>

    <!-- Grille Responsive 4 Colonnes -->
    <?php if (empty($episodes)): ?>
        <div class="p-5 text-center rounded-4 my-4" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
            <span class="material-symbols-rounded text-muted" style="font-size: 3.5rem;">search_off</span>
            <h4 class="mt-3 text-white fw-bold">Aucun épisode trouvé</h4>
            <p class="text-muted small">Essayez un autre terme de recherche ou réinitialisez les filtres.</p>
            <a href="episodes.php" class="btn-cta-electric mt-2">Réinitialiser les filtres</a>
        </div>
    <?php else: ?>
        <div class="episodes-cinematic-grid">
            <?php foreach ($episodes as $ep): ?>
                <?php
                    $epJson = json_encode([
                        'id'               => $ep['id'],
                        'title'            => $ep['title'],
                        'ep_number'        => $ep['ep_number'] ?? ('EP. ' . str_pad($ep['id'], 2, '0', STR_PAD_LEFT)),
                        'podcast_title'    => $ep['podcast_title'],
                        'podcast_category' => $ep['podcast_category'] ?? '',
                        'cover'            => $ep['cover'],
                        'youtube_id'       => $ep['youtube_id'],
                        'duration'         => $ep['duration'],
                        'date'             => $ep['date'],
                        'host'             => $ep['host'],
                        'description'      => $ep['description'],
                        'contributors'     => $ep['contributors'] ?? [],
                        'chapters'         => $ep['chapters'] ?? [],
                        'filiere'          => $ep['filiere'] ?? '',
                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
                ?>
                <div class="card-episode-cinematic filterable-episode-card searchable-item"
                     data-category="<?= e($ep['podcast_category'] ?? '') ?>">
                    
                    <!-- Cover 16:10 avec Badges & Overlay -->
                    <div class="card-artwork-wrap"
                         role="button" tabindex="0" title="Voir les détails"
                         data-open-episode-modal="<?= htmlspecialchars(rawurlencode($epJson), ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= e($ep['cover']) ?>" alt="<?= e($ep['title']) ?>" class="card-artwork-img" loading="lazy">
                        
                        <span class="card-badge-category">
                            <?= e($ep['podcast_category']) ?>
                        </span>

                        <span class="card-badge-duration">
                            <?= e($ep['duration']) ?>
                        </span>

                        <div class="card-cover-play-overlay">
                            <span class="material-symbols-rounded">play_arrow</span>
                        </div>
                    </div>

                    <!-- Contenu Textuel -->
                    <div class="card-body-content">
                        <div class="card-meta-line">
                            <span><?= e($ep['podcast_title']) ?></span>
                            <span>•</span>
                            <span><?= e($ep['date']) ?></span>
                        </div>

                        <h3 class="card-episode-title">
                            <a href="#" role="button"
                               data-open-episode-modal="<?= htmlspecialchars(rawurlencode($epJson), ENT_QUOTES, 'UTF-8') ?>">
                                <?= e($ep['title']) ?>
                            </a>
                        </h3>

                        <!-- Badges Contributeurs -->
                        <?php if (!empty($ep['contributors'])): ?>
                            <div class="card-tags-list mb-3">
                                <?php foreach (array_slice($ep['contributors'], 0, 2) as $c): ?>
                                    <span class="tag-badge-pill"><?= e($c) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($ep['contributors']) > 2): ?>
                                    <span class="tag-badge-pill">+<?= count($ep['contributors']) - 2 ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Footer d'Action -->
                        <div class="card-footer-action">
                            <button class="btn btn-sm text-muted p-0 d-inline-flex align-items-center gap-1 border-0 bg-transparent text-decoration-none"
                                    style="font-size: 0.76rem;"
                                    data-open-episode-modal="<?= htmlspecialchars(rawurlencode($epJson), ENT_QUOTES, 'UTF-8') ?>">
                                <span class="material-symbols-rounded" style="font-size: 0.95rem;">info</span>
                                <span>Détails</span>
                            </button>

                            <div class="d-flex align-items-center gap-2">
                                <!-- Bouton Partager & QR Code -->
                                <button class="btn-card-share-inline btn-share-action"
                                        data-episode-id="<?= $ep['id'] ?>"
                                        data-episode-json="<?= htmlspecialchars(rawurlencode($epJson), ENT_QUOTES, 'UTF-8') ?>"
                                        title="Partager & QR Code">
                                    <span class="material-symbols-rounded" style="font-size: 1.05rem;">qr_code_2</span>
                                </button>

                                <!-- Bouton Lecture Audio Direct -->
                                <button class="btn-card-play"
                                        data-play-episode
                                        data-episode-id="<?= $ep['id'] ?>"
                                        data-title="<?= e($ep['title']) ?>"
                                        data-host="<?= e($ep['host']) ?>"
                                        data-podcast="<?= e($ep['podcast_title']) ?>"
                                        data-cover="<?= e($ep['cover']) ?>"
                                        data-youtube-id="<?= e($ep['youtube_id']) ?>"
                                        data-duration="<?= e($ep['duration']) ?>"
                                        title="Écouter cet épisode">
                                    <span class="material-symbols-rounded">play_arrow</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
