<?php
/**
 * Page Détail d'un Épisode - Micro ENSAD Casablanca
 * Redesign Sombre Cinématique Éditorial
 */
require_once __DIR__ . '/includes/functions.php';

$episodeId = $_GET['id'] ?? null;
if (!$episodeId) {
    header('Location: episodes.php');
    exit;
}

$episode = getEpisodeById($episodeId);
if (!$episode) {
    header('Location: episodes.php');
    exit;
}

$chapters = getChaptersByEpisodeId($episode['id']);
$allEpisodes = getEpisodes();
$relatedEpisodes = array_filter($allEpisodes, fn($e) => $e['id'] != $episode['id']);
$relatedEpisodes = array_slice($relatedEpisodes, 0, 4);

$pageTitle = htmlspecialchars_decode(e($episode['title'])) . " — Micro ENSAD Podcast";
$pageDescription = !empty($episode['description']) ? mb_substr(strip_tags($episode['description']), 0, 160) : "Écoutez cet épisode du podcast Micro ENSAD.";
// Make cover image absolute for Open Graph
$_coverRaw = $episode['cover'] ?? '';
if (!empty($_coverRaw) && !str_starts_with($_coverRaw, 'http')) {
    $pageImage = 'https://micro-ensad.ma/' . ltrim($_coverRaw, '/');
} else {
    $pageImage = !empty($_coverRaw) ? $_coverRaw : 'https://micro-ensad.ma/assets/images/hero-mic-cinematic.jpg';
}
$pageUrl = 'https://micro-ensad.ma/episode-detail.php?id=' . (int)($episode['id'] ?? 0);
$activeNav = 'episodes';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4 py-lg-5">

    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item"><a href="episodes.php" class="text-muted text-decoration-none">Épisodes</a></li>
            <li class="breadcrumb-item active text-truncate text-white" style="max-width: 340px;" aria-current="page"><?= e($episode['title']) ?></li>
        </ol>
    </nav>

    <!-- 1. CARTE HERO DE L'ÉPISODE (Éditoriale Sombre) -->
    <div class="featured-episode-panel mb-5">
        <div class="row align-items-center g-4 g-lg-5">
            
            <!-- Image de couverture Carrée 1:1 avec Badge -->
            <div class="col-12 col-md-5 col-lg-4 text-center">
                <div class="position-relative d-inline-block w-100" style="max-width: 320px; aspect-ratio: 1 / 1; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.6);">
                    <img src="<?= e($episode['cover']) ?>" alt="<?= e($episode['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="featured-ep-tag-corner"><?= e($episode['ep_number'] ?? 'EP') ?></span>
                </div>
            </div>

            <!-- Détails de l'épisode -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge-vedette"><?= e($episode['ep_number'] ?? 'EP. 01') ?></span>
                    <span class="badge bg-secondary bg-opacity-25 text-white-50 px-2 py-1 rounded font-monospace" style="font-size: 0.72rem;">
                        <?= e($episode['podcast_category']) ?>
                    </span>
                    <span class="badge bg-dark border border-secondary border-opacity-25 text-muted px-2 py-1 rounded font-monospace" style="font-size: 0.72rem;">
                        YouTube Audio HD
                    </span>
                </div>

                <h1 class="h2 fw-bold text-white mb-3" style="line-height: 1.25; letter-spacing: -0.02em;">
                    <?= e($episode['title']) ?>
                </h1>

                <!-- Équipe étudiante & Encadrement -->
                <div class="p-3 rounded-3 mb-4" style="background: var(--bg-surface-elevated); border: 1px solid var(--border-color);">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-7">
                            <div class="text-muted small mb-1" style="font-size: 0.75rem;">👥 Étudiants Réalisateurs &amp; Intervenants :</div>
                            <div class="d-flex flex-wrap gap-1">
                                <?php if (!empty($episode['contributors'])): ?>
                                    <?php foreach ($episode['contributors'] as $c): ?>
                                        <span class="tag-badge-pill"><?= e($c) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <strong class="text-white small"><?= e($episode['host']) ?></strong>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 text-md-end">
                            <div class="text-muted small" style="font-size: 0.75rem;">🎓 Encadrement :</div>
                            <div class="fw-bold small text-white"><?= e($episode['encadrement'] ?? 'Mme Randa El Amraoui') ?></div>
                            <div class="text-muted font-monospace" style="font-size: 0.72rem;"><?= e($episode['department'] ?? 'DENSAD Bac+5') ?></div>
                        </div>
                    </div>
                </div>

                <p class="text-muted mb-4 fs-6" style="line-height: 1.8;">
                    <?= nl2br(e($episode['description'])) ?>
                </p>

                <!-- Boutons d'action -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <button class="btn-hero-primary"
                            data-play-episode
                            data-episode-id="<?= $episode['id'] ?>"
                            data-title="<?= e($episode['title']) ?>"
                            data-host="<?= e($episode['host']) ?>"
                            data-podcast="<?= e($episode['podcast_title']) ?>"
                            data-cover="<?= e($episode['cover']) ?>"
                            data-youtube-id="<?= e($episode['youtube_id']) ?>"
                            data-duration="<?= e($episode['duration']) ?>">
                        <span class="material-symbols-rounded" style="font-size: 1.25rem;">play_arrow</span>
                        <span>Lancer l'Écoute Audio (<?= e($episode['duration']) ?>)</span>
                    </button>

                    <?php
                    $detailEpJson = json_encode([
                        'id'               => $episode['id'],
                        'ep_number'        => $episode['ep_number'] ?? '',
                        'podcast_title'    => $episode['podcast_title'],
                        'podcast_category' => $episode['podcast_category'],
                        'title'            => $episode['title'],
                        'cover'            => $episode['cover'],
                        'duration'         => $episode['duration'],
                        'date'             => $episode['date'],
                        'host'             => $episode['host'],
                        'description'      => $episode['description'],
                        'contributors'     => $episode['contributors'] ?? [],
                        'chapters'         => $episode['chapters'] ?? [],
                        'filiere'          => $episode['filiere'] ?? '',
                        'department'       => $episode['department'] ?? '',
                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
                    ?>
                    <button class="btn-featured-share btn-share-action d-inline-flex align-items-center gap-2"
                            data-episode-id="<?= $episode['id'] ?>"
                            data-episode-json="<?= htmlspecialchars(rawurlencode($detailEpJson), ENT_QUOTES, 'UTF-8') ?>"
                            title="Partager cet épisode & QR Code">
                        <span class="material-symbols-rounded" style="font-size: 1.1rem;">qr_code_2</span>
                        <span>Partager & QR Code</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. CONTENU DÉTAILLÉ (Notes d'émission & Chapitres) -->
    <div class="row g-4 mb-5">
        
        <!-- Notes d'Émission -->
        <div class="col-12 col-lg-8">
            <div class="p-4 p-md-5 rounded-4 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom" style="border-color: var(--border-color)!important;">
                    <span class="material-symbols-rounded text-primary fs-4">article</span>
                    <h2 class="h4 fw-bold text-white mb-0">Notes &amp; Thématiques de l'Épisode</h2>
                </div>

                <div class="show-notes-content text-muted" style="line-height: 1.8;">
                    <?= renderMarkdown($episode['show_notes'] ?? '') ?>
                </div>
            </div>
        </div>

        <!-- Chapitres & Repères Temporels -->
        <div class="col-12 col-lg-4">
            <div class="p-4 rounded-4" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: var(--border-color)!important;">
                    <span class="material-symbols-rounded text-primary fs-4">format_list_bulleted</span>
                    <h3 class="h5 fw-bold text-white mb-0">Chapitres Audio</h3>
                </div>

                <p class="text-muted small mb-3">Cliquez sur un repère pour démarrer l'écoute au minutage précis :</p>

                <?php if (!empty($chapters)): ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($chapters as $chap): ?>
                            <button class="btn text-start p-2 rounded-3 d-flex align-items-center justify-content-between chapter-seek-btn"
                                    data-seek-time="<?= $chap['seconds'] ?>"
                                    data-play-episode
                                    data-episode-id="<?= $episode['id'] ?>"
                                    data-title="<?= e($episode['title']) ?>"
                                    data-host="<?= e($episode['host']) ?>"
                                    data-podcast="<?= e($episode['podcast_title']) ?>"
                                    data-cover="<?= e($episode['cover']) ?>"
                                    data-youtube-id="<?= e($episode['youtube_id']) ?>"
                                    data-duration="<?= e($episode['duration']) ?>"
                                    style="background: var(--bg-surface-elevated); border: 1px solid var(--border-color); color: var(--text-body); transition: all 0.2s;">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <span class="material-symbols-rounded text-primary" style="font-size: 1.1rem;">play_circle</span>
                                    <span class="small text-truncate text-white"><?= e($chap['title']) ?></span>
                                </div>
                                <span class="badge font-monospace ms-2" style="background: rgba(18, 100, 255, 0.2); color: var(--electric-blue-light);"><?= e($chap['time']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted small">
                        <span class="material-symbols-rounded fs-2 mb-2 d-block text-primary">graphic_eq</span>
                        Écoute continue intégrale (durée : <?= e($episode['duration']) ?>)
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- 3. AUTRES ÉPISODES CONSEILLÉS (Grille Cinématique) -->
    <?php if (!empty($relatedEpisodes)): ?>
        <section class="mb-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h3 class="section-heading-editorial">Autres épisodes à découvrir</h3>
                <span class="section-accent-bar"></span>
            </div>

            <div class="episodes-cinematic-grid">
                <?php foreach ($relatedEpisodes as $rel): ?>
                    <div class="card-episode-cinematic">
                        <div class="card-artwork-wrap">
                            <a href="episode-detail.php?id=<?= $rel['id'] ?>">
                                <img src="<?= e($rel['cover']) ?>" alt="<?= e($rel['title']) ?>" class="card-artwork-img" loading="lazy">
                            </a>
                            <span class="card-badge-category"><?= e($rel['podcast_category']) ?></span>
                            <span class="card-badge-duration"><?= e($rel['duration']) ?></span>
                        </div>
                        <div class="card-body-content">
                            <div class="card-meta-line">
                                <span><?= e($rel['podcast_title']) ?></span>
                            </div>
                            <h4 class="card-episode-title">
                                <a href="episode-detail.php?id=<?= $rel['id'] ?>"><?= e($rel['title']) ?></a>
                            </h4>
                            <div class="card-footer-action">
                                <a href="episode-detail.php?id=<?= $rel['id'] ?>" class="text-muted small text-decoration-none">Détails</a>
                                <button class="btn-card-play"
                                        data-play-episode
                                        data-episode-id="<?= $rel['id'] ?>"
                                        data-title="<?= e($rel['title']) ?>"
                                        data-host="<?= e($rel['host']) ?>"
                                        data-podcast="<?= e($rel['podcast_title']) ?>"
                                        data-cover="<?= e($rel['cover']) ?>"
                                        data-youtube-id="<?= e($rel['youtube_id']) ?>"
                                        data-duration="<?= e($rel['duration']) ?>"
                                        title="Écouter cet épisode">
                                    <span class="material-symbols-rounded">play_arrow</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
