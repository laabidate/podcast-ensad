<?php
/**
 * Détail d'une Émission - Micro ENSAD Mohammedia
 * Dual Mode UX/UI avec Icônes Google Material Symbols et Boîtes Carrées
 */
require_once __DIR__ . '/includes/functions.php';

$podcastId = $_GET['id'] ?? null;
if (!$podcastId) {
    header('Location: podcasts.php');
    exit;
}

$podcast = getPodcastById($podcastId);
if (!$podcast) {
    header('Location: podcasts.php');
    exit;
}

$episodes = getEpisodes(null, $podcast['id']);
$pageTitle = e($podcast['title']) . " • Micro ENSAD Mohammedia";
$activeNav = 'podcasts';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4 py-lg-5">

    <a href="podcasts.php" class="btn btn-sm btn-ensad-outline rounded-pill mb-4 d-inline-flex align-items-center gap-1">
        <span class="material-symbols-rounded" style="font-size: 1rem;">arrow_back</span>
        <span>Toutes les émissions</span>
    </a>

    <!-- Bannière Série -->
    <div class="p-4 p-md-5 rounded-4 mb-5 shadow-sm border" style="background: var(--bg-surface); border-color: var(--border-color)!important;">
        <div class="row align-items-center g-4 g-lg-5">
            <!-- Vignette Carrée 1:1 -->
            <div class="col-12 col-md-4 text-center">
                <img src="<?= e($podcast['cover']) ?>" alt="<?= e($podcast['title']) ?>" class="featured-img-thumb shadow" style="aspect-ratio: 1 / 1; width: 100%; max-width: 280px; object-fit: cover;">
            </div>
            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-3 py-1 rounded-pill font-fredoka">
                        <?= e($podcast['category']) ?>
                    </span>
                    <span class="text-muted small d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded text-primary" style="font-size: 1rem;">school</span>
                        <span><?= e($podcast['department']) ?></span>
                    </span>
                </div>

                <h1 class="h2 font-fredoka fw-bold mb-2" style="color: var(--text-main);"><?= e($podcast['title']) ?></h1>

                <p class="text-muted small mb-3">
                    Animé par <strong style="color: var(--text-main);"><?= e($podcast['hosts']) ?></strong> • <?= e($podcast['followers']) ?> auditeurs réguliers
                </p>

                <p class="text-muted mb-4" style="line-height: 1.75;">
                    <?= e($podcast['description']) ?>
                </p>

                <?php if (!empty($episodes)): ?>
                    <button class="btn btn-ensad-yellow py-2 px-4 shadow"
                            data-play-episode
                            data-episode-id="<?= $episodes[0]['id'] ?>"
                            data-title="<?= e($episodes[0]['title']) ?>"
                            data-host="<?= e($episodes[0]['host']) ?>"
                            data-podcast="<?= e($podcast['title']) ?>"
                            data-cover="<?= e($episodes[0]['cover']) ?>"
                            data-youtube-id="<?= e($episodes[0]['youtube_id']) ?>"
                            data-duration="<?= e($episodes[0]['duration']) ?>">
                        <span class="material-symbols-rounded fs-4">play_arrow</span>
                        <span>Écouter le 1er épisode</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Liste des Épisodes de la Série -->
    <h2 class="h4 font-fredoka fw-bold mb-4" style="color: var(--text-main);">
        Épisodes de la Série (<?= count($episodes) ?>)
    </h2>

    <div class="row g-3">
        <?php foreach ($episodes as $ep): ?>
            <div class="col-12">
                <div class="p-3 rounded-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 border" 
                     style="background: var(--bg-surface); border-color: var(--border-color)!important;"
                     id="episode-card-<?= $ep['id'] ?>">
                    
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-sm btn-ensad-yellow rounded-circle p-0 flex-shrink-0"
                                style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;"
                                data-play-episode
                                data-episode-id="<?= $ep['id'] ?>"
                                data-title="<?= e($ep['title']) ?>"
                                data-host="<?= e($ep['host']) ?>"
                                data-podcast="<?= e($podcast['title']) ?>"
                                data-cover="<?= e($ep['cover']) ?>"
                                data-youtube-id="<?= e($ep['youtube_id']) ?>"
                                data-duration="<?= e($ep['duration']) ?>">
                            <span class="material-symbols-rounded fs-4">play_arrow</span>
                        </button>

                        <div>
                            <span class="badge bg-light text-primary border font-monospace" style="font-size: 0.68rem;"><?= e($ep['ep_number'] ?? 'EP. 00' . $ep['id']) ?></span>
                            <h3 class="h6 font-fredoka fw-bold mb-1">
                                <a href="episode-detail.php?id=<?= $ep['id'] ?>" class="text-decoration-none" style="color: var(--text-main);">
                                    <?= e($ep['title']) ?>
                                </a>
                            </h3>
                            <?php if (!empty($ep['contributors'])): ?>
                                <div class="d-flex flex-wrap gap-1 mt-1 mb-1">
                                    <?php foreach ($ep['contributors'] as $c): ?>
                                        <span class="badge-contrib" style="font-size: 0.68rem; padding: 2px 7px;"><?= e($c) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-muted small font-monospace"><?= e($ep['date']) ?> • <?= e($ep['podcast_category']) ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 flex-shrink-0 ms-sm-3">
                        <span class="font-monospace text-muted small d-inline-flex align-items-center gap-1">
                            <span class="material-symbols-rounded text-primary" style="font-size: 1rem;">schedule</span>
                            <span><?= e($ep['duration']) ?></span>
                        </span>
                        <a href="episode-detail.php?id=<?= $ep['id'] ?>" class="btn btn-sm btn-ensad-outline rounded-pill px-3 d-inline-flex align-items-center gap-1">
                            <span>Détails</span>
                            <span class="material-symbols-rounded" style="font-size: 1rem;">chevron_right</span>
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
