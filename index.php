<?php
/**
 * Page d'Accueil - Micro ENSAD Casablanca
 * Redesign Cinématique Éditorial Sombre
 * Palette : #050B14, #071426, #0B1F3A, #1264FF, #2F7BFF, #FFFFFF, #A8B7CC
 * Typographies : Plus Jakarta Sans, Montserrat, Caveat
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Micro ENSAD • Le Podcast Officiel des Étudiants";
$activeNav = 'home';

// Données statiques officielles
$allEpisodes = getEpisodes();
// Épisode à la une : Épisode #11 (ou le plus récent)
$featuredEpisode = !empty($allEpisodes) ? $allEpisodes[0] : null;
$institution = getInstitution();
$categories = getCategories();
$stats = getStats();

// Charger les paramètres personnalisables du site (data/site-settings.json)
$_siteSettings = [];
$_siteSettingsFile = __DIR__ . '/data/site-settings.json';
if (file_exists($_siteSettingsFile)) {
    $_raw = file_get_contents($_siteSettingsFile);
    $_siteSettings = json_decode($_raw, true) ?? [];
}
$_welcomeSettings = $_siteSettings['welcome_page'] ?? [];
$_randaSettings   = $_siteSettings['randa_intro']   ?? [];
$_heroSettings    = $_siteSettings['hero']         ?? [];
$_ctaSettings     = $_siteSettings['cta_banner']   ?? [];

// ── Redirection vers la Page de Bienvenue (Welcome Splash Page) ──
if (!empty($_welcomeSettings['enabled'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Si l'utilisateur clique sur "Start", un paramètre ?enter=1 ou ?skip_welcome=1 est passé
    if (isset($_GET['enter']) || isset($_GET['skip_welcome'])) {
        $_SESSION['ensad_welcome_passed'] = true;
        setcookie('ensad_welcome_passed', '1', time() + (86400 * 30), '/');
    } elseif (empty($_SESSION['ensad_welcome_passed']) && empty($_COOKIE['ensad_welcome_passed'])) {
        header('Location: welcome.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-3 py-lg-4">

    <!-- 1. HERO SECTION CINÉMATIQUE (2 COLONNES) -->
    <section class="hero-cinematic pb-4">
        <div class="hero-panel-wrapper" style="background: var(--bg-surface); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); overflow: hidden; position: relative;">
            <div class="row align-items-center g-4 g-lg-5">
            
            <!-- Colonne Gauche : Slogan, Titre & Identité ENSAD -->
            <div class="col-12 col-lg-6">
                <h1 class="hero-main-title">
                    <?= e($_heroSettings['title'] ?? 'Micro ENSAD') ?><br>
                    <span style="color: #A8B7CC; font-weight: 700; font-size: 0.78em;"><?= e($_heroSettings['subtitle'] ?? 'Le Podcast des Étudiants') ?></span>
                </h1>

                <p class="hero-description">
                    <?= e($_heroSettings['description'] ?? "Découvrez les réflexions, projets et créations des étudiants de l'École Nationale Supérieure d'Art et de Design de Casablanca. Une immersion sonore au cœur de la créativité.") ?>
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    <?php if ($featuredEpisode): ?>
                        <button class="btn-hero-primary"
                                data-play-episode
                                data-episode-id="<?= $featuredEpisode['id'] ?>"
                                data-title="<?= e($featuredEpisode['title']) ?>"
                                data-host="<?= e($featuredEpisode['host']) ?>"
                                data-podcast="<?= e($featuredEpisode['podcast_title']) ?>"
                                data-cover="<?= e($featuredEpisode['cover']) ?>"
                                data-youtube-id="<?= e($featuredEpisode['youtube_id']) ?>"
                                data-duration="<?= e($featuredEpisode['duration']) ?>">
                            <span class="material-symbols-rounded" style="font-size: 1.25rem;">play_arrow</span>
                            <span><?= e($_heroSettings['cta_listen_text'] ?? 'Écouter le dernier épisode') ?></span>
                        </button>
                    <?php endif; ?>
                    <a href="#dernieres-episodes-section" class="btn-hero-secondary">
                        <span class="material-symbols-rounded" style="font-size: 1.2rem;">podcasts</span>
                        <span><?= e($_heroSettings['cta_discover_text'] ?? 'Découvrir les épisodes') ?></span>
                    </a>
                </div>

                <!-- Capsule Audio de Présentation — Mme Randa El Amraoui -->
                <?php 
                $randaHeroEnabled = !empty($_randaSettings['enabled']) && !empty($_randaSettings['show_in_hero']);
                if ($randaHeroEnabled): 
                    $rName   = $_randaSettings['name'] ?? 'Mme Randa El Amraoui';
                    $rRole   = $_randaSettings['role'] ?? 'Responsable du Projet • Module de Français (Bac+5)';
                    $rTitle  = $_randaSettings['title'] ?? 'Mot de Présentation du Projet';
                    $rAudio  = $_randaSettings['audio_url'] ?? '';
                    $rAvatar = $_randaSettings['avatar_image'] ?? 'assets/images/micro-ensad-icon.svg';
                    $rQuote  = $_randaSettings['quote'] ?? '';
                    
                    $rAudioSrc = !empty($rAudio) ? ((str_starts_with($rAudio, 'http')) ? $rAudio : ltrim($rAudio, '/')) : '';
                    $rAvatarSrc = (str_starts_with($rAvatar, 'http')) ? $rAvatar : ltrim($rAvatar, '/');
                    if (!str_starts_with($rAvatarSrc, 'http') && !file_exists(__DIR__ . '/' . $rAvatarSrc)) {
                        $rAvatarSrc = 'assets/images/micro-ensad-icon.svg';
                    }
                ?>
                <div class="hero-randa-card mt-4 p-3 rounded-4" style="background: var(--bg-surface); border: 1px solid rgba(18, 100, 255, 0.15); box-shadow: 0 4px 20px rgba(18,100,255,0.06); width: 100%;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Avatar avec badge micro -->
                        <div class="position-relative flex-shrink-0">
                            <img src="<?= e($rAvatarSrc) ?>" alt="<?= e($rName) ?>" 
                                 class="rounded-circle border border-2 border-primary shadow-sm" 
                                 style="width: 50px; height: 50px; object-fit: cover; background: #f0f0f0;">
                            <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-dark"
                                  style="width: 20px; height: 20px; font-size: 0.65rem;">
                                <i class="bi bi-mic-fill"></i>
                            </span>
                        </div>

                        <!-- Textes & Rôle -->
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0" style="font-size: 0.65rem; letter-spacing: 0.05em; font-weight: 700;">
                                    ENCADRANTE DU PROJET
                                </span>
                                <span class="fw-bold small" style="color: var(--text-main); white-space: normal;"><?= e($rName) ?></span>
                            </div>
                            <div class="small fw-semibold mt-1" style="font-size: 0.82rem; color: var(--text-main); white-space: normal; line-height: 1.3;"><?= e($rTitle) ?></div>
                            <div class="mt-1" style="font-size: 0.72rem; color: var(--text-body); white-space: normal; line-height: 1.3;"><?= e($rRole) ?></div>
                        </div>

                        <!-- Bouton Lecture Audio -->
                        <button type="button" id="btnPlayRandaHero" class="btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0 shadow"
                                style="width: 44px; height: 44px; background: linear-gradient(135deg, #1264FF, #2F7BFF); border: none;"
                                onclick="toggleRandaHeroAudio()"
                                title="Écouter le mot de Mme Randa">
                            <i id="iconRandaHero" class="bi bi-play-fill fs-4 text-white"></i>
                        </button>
                    </div>

                    <!-- Lecteur audio HTML5 & Barre de progression -->
                    <audio id="audioRandaHero" src="<?= e($rAudioSrc) ?>" preload="metadata"></audio>
                    <div id="progressWrapRandaHero" class="mt-2 pt-1 d-flex align-items-center gap-2" style="display: none !important;">
                        <small id="timeRandaHero" class="text-muted" style="font-size: 0.72rem; min-width: 34px; font-variant-numeric: tabular-nums;">00:00</small>
                        <div class="progress flex-grow-1" style="height: 5px; background: rgba(128,128,128,0.2); cursor: pointer; border-radius: 999px;" onclick="seekRandaHeroAudio(event)">
                            <div id="progressBarRandaHero" class="progress-bar bg-primary" role="progressbar" style="width: 0%; border-radius: 999px;"></div>
                        </div>
                        <small id="durationRandaHero" class="text-muted" style="font-size: 0.72rem; min-width: 34px; font-variant-numeric: tabular-nums;">--:--</small>
                    </div>

                    <?php if (!empty($rQuote)): ?>
                        <div class="mt-2 pt-2 border-top border-secondary border-opacity-20 fst-italic" style="font-size: 0.78rem; line-height: 1.45; color: var(--text-body);">
                            « <?= e($rQuote) ?> »
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Colonne Droite : Composition Visuelle Microphone Studio, Waveform & Annotations Manuscrites -->
            <div class="col-12 col-lg-6">
                <div class="hero-visual-stage">
                    <!-- Forme Géométrique d'arrière-plan en angle -->
                    <div class="hero-backdrop-geo"></div>

                    <!-- Carte Microphone Studio Cinématique -->
                    <div class="hero-mic-card">
                        <img src="assets/images/hero-mic-cinematic.jpg" alt="Studio Micro ENSAD" class="hero-mic-photo">

                        <!-- Waveform animée en bas de la photographie -->
                        <div class="hero-mic-wave-overlay">
                            <?php for ($w = 0; $w < 26; $w++): ?>
                                <span class="hero-wave-stick"></span>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Annotations Manuscrites Flottantes (Police Caveat) -->
                    <span class="handwritten-annotation annotation-creation">Création</span>
                    <span class="handwritten-annotation annotation-design">Design</span>
                    <span class="handwritten-annotation annotation-cinema">Cinéma</span>
                    <span class="handwritten-annotation annotation-art">Art...</span>
                </div>
            </div>

        </div>
        </div> <!-- End hero-panel-wrapper -->
    </section>


    <!-- 2. SECTION : ÉPISODES EN VEDETTE (SLIDER) -->
    <?php if (!empty($allEpisodes)): ?>
    <section class="mb-5 pb-2 position-relative" style="padding-left: 5px; padding-right: 5px;">
        
        <div id="featuredEpisodesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-inner" style="border-radius: 20px; box-shadow: var(--shadow-lg);">
                <?php foreach ($allEpisodes as $index => $episode): 
                    // PRÉPARATION DES DONNÉES JSON POUR LA MODALE
                    $featuredJson = json_encode([
                        'id'               => $episode['id'],
                        'youtube_id'       => $episode['youtube_id'],
                        'title'            => $episode['title'],
                        'podcast_title'    => $episode['podcast_title'],
                        'podcast_category' => $episode['podcast_category'] ?? 'Art & Performance',
                        'ep_number'        => $episode['ep_number'] ?? ('#' . (count($allEpisodes) - $index)),
                        'cover'            => $episode['cover'],
                        'duration'         => $episode['duration'],
                        'date'             => $episode['date'],
                        'host'             => $episode['host'],
                        'description'      => $episode['description'],
                        'contributors'     => $episode['contributors'] ?? [],
                        'chapters'         => $episode['chapters'] ?? [],
                        'filiere'          => $episode['filiere'] ?? '',
                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
                ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="featured-episode-panel" style="box-shadow: none; border-radius: 0; margin: 0; border: 1px solid var(--border-color); border-left: none; border-right: none; border-top: none; border-bottom: none;">
                        <div class="row align-items-center g-4">
                            
                            <!-- Bloc Gauche : Artwork & Contenu -->
                            <div class="col-12 col-lg-8">
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-4">
                                    
                                    <!-- Vignette Carrée 1:1 avec Badge -->
                                    <div class="featured-artwork-box"
                                         role="button" tabindex="0" title="Voir les détails"
                                         data-open-episode-modal="<?= htmlspecialchars(rawurlencode($featuredJson), ENT_QUOTES, 'UTF-8') ?>"
                                         style="cursor: pointer;">
                                        <img src="<?= e($episode['cover']) ?>" alt="<?= e($episode['title']) ?>" class="featured-artwork-img">
                                        <span class="featured-ep-tag-corner"><?= e($episode['ep_number'] ?? ('#' . (count($allEpisodes) - $index))) ?></span>
                                    </div>

                                    <!-- Métadonnées & Actions -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge-vedette">ÉPISODE <?= e($episode['ep_number'] ?? ('#' . (count($allEpisodes) - $index))) ?></span>
                                            <span class="badge bg-secondary bg-opacity-25 text-white-50 px-2 py-1 rounded font-monospace" style="font-size: 0.72rem;">
                                                <?= e($episode['podcast_category'] ?? 'Art & Performance') ?>
                                            </span>
                                        </div>

                                        <h2 class="featured-title mb-2">
                                            <a href="#" role="button" class="text-white text-decoration-none"
                                               data-open-episode-modal="<?= htmlspecialchars(rawurlencode($featuredJson), ENT_QUOTES, 'UTF-8') ?>">
                                                <?= e($episode['title']) ?>
                                            </a>
                                        </h2>

                                        <div class="featured-meta-row mb-3">
                                            <span>Par <strong class="text-white"><?= e($episode['podcast_title']) ?></strong></span>
                                            <span>•</span>
                                            <span>Durée: <?= e($episode['duration']) ?></span>
                                            <span>•</span>
                                            <span>Cycle Bac+5 DENSAD</span>
                                        </div>

                                        <p class="featured-description mb-3">
                                            <?= e($episode['description']) ?>
                                        </p>

                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <button class="btn-featured-play"
                                                    data-play-episode
                                                    data-episode-id="<?= $episode['id'] ?>"
                                                    data-title="<?= e($episode['title']) ?>"
                                                    data-host="<?= e($episode['host']) ?>"
                                                    data-podcast="<?= e($episode['podcast_title']) ?>"
                                                    data-cover="<?= e($episode['cover']) ?>"
                                                    data-youtube-id="<?= e($episode['youtube_id']) ?>"
                                                    data-duration="<?= e($episode['duration']) ?>">
                                                <span class="material-symbols-rounded">play_arrow</span>
                                                <span>Écouter l'épisode</span>
                                            </button>

                                            <button class="btn-featured-share"
                                                    data-open-episode-modal="<?= htmlspecialchars(rawurlencode($featuredJson), ENT_QUOTES, 'UTF-8') ?>">
                                                <span class="material-symbols-rounded" style="font-size: 1rem;">info</span>
                                                <span>Détails</span>
                                            </button>

                                            <button class="btn-featured-share btn-share-action d-inline-flex align-items-center gap-1"
                                                    data-episode-id="<?= $episode['id'] ?>"
                                                    data-episode-json="<?= htmlspecialchars(rawurlencode($featuredJson), ENT_QUOTES, 'UTF-8') ?>"
                                                    title="Partager cet épisode & QR Code">
                                                <span class="material-symbols-rounded" style="font-size: 1.05rem;">qr_code_2</span>
                                                <span>Partager</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Bloc Droit : Compteur d'Épisodes & Présentation -->
                            <div class="col-12 col-lg-4">
                                <div class="featured-stats-card">
                                    <div class="stat-huge-number"><?= count($allEpisodes) ?></div>
                                    <div class="stat-label-main mb-1">Épisodes en Ligne</div>
                                    <div class="stat-filter-summary mb-3">
                                        Filières DGI &amp; GDA • Audio pur extrait de YouTube
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="episodes.php" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 font-monospace" style="font-size: 0.76rem; border-color: rgba(255,255,255,0.2);">
                                            <span>Explorer le catalogue complet</span>
                                            <span class="material-symbols-rounded ms-1" style="font-size: 0.9rem;">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>



        </div>
        

    </section>
    <?php endif; ?>

    <!-- 3. SECTION DERNIERS ÉPISODES (GRILLE RESPONSIVE 4 COLONNES) -->
    <section id="dernieres-episodes-section" class="mb-5 pb-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <h2 class="section-heading-editorial">Derniers épisodes</h2>
                <span class="section-accent-bar"></span>
            </div>
            <a href="episodes.php" class="link-editorial-more">
                <span>Voir tous les épisodes</span>
                <span class="material-symbols-rounded" style="font-size: 1.1rem;">arrow_forward</span>
            </a>
        </div>

        <div class="episodes-cinematic-grid" id="episodesCinematicGrid">
            <?php foreach ($allEpisodes as $ep): ?>
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
                    
                    <!-- Visuel de Couverture (16:10 / 1:1) avec Badges & Play Overlay -->
                    <div class="card-artwork-wrap"
                         role="button" tabindex="0" title="Voir les détails"
                         data-open-episode-modal="<?= htmlspecialchars(rawurlencode($epJson), ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= e($ep['cover']) ?>" alt="<?= e($ep['title']) ?>" class="card-artwork-img" loading="lazy">
                        
                        <!-- Badge Catégorie -->
                        <span class="card-badge-category">
                            <?= e($ep['podcast_category']) ?>
                        </span>

                        <!-- Badge Durée -->
                        <span class="card-badge-duration">
                            <?= e($ep['duration']) ?>
                        </span>

                        <!-- Play Icon Overlay -->
                        <div class="card-cover-play-overlay">
                            <span class="material-symbols-rounded">play_arrow</span>
                        </div>
                    </div>

                    <!-- Contenu Textuel de la Carte -->
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

                        <!-- Badges Contributeurs Étudiants -->
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

                        <!-- Footer d'Action de la Carte -->
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

                                <!-- Bouton Play Audio Direct -->
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
    </section>

    <!-- 4. PARCOURIR PAR THÉMATIQUE (FILTRAGE SANS RECHARGEMENT) -->
    <section id="thematiques-section" class="mb-5 pb-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-3">
                <h2 class="section-heading-editorial">Parcourir par thématique</h2>
                <span class="section-accent-bar"></span>
            </div>
            <span class="text-muted small d-none d-md-inline">12 filières &amp; thématiques officielles</span>
        </div>

        <div class="thematic-filter-container" id="thematicFilterContainer">
            <button type="button" class="filter-pill-cinematic active" data-filter-category="all">
                Tous les épisodes
            </button>
            <?php foreach ($categories as $cat): ?>
                <button type="button" class="filter-pill-cinematic" data-filter-category="<?= e($cat) ?>">
                    <?= e($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 5. BANNIÈRE CONTACT / PARTICIPER AU PROJET (CTA) -->
    <section class="cta-cinematic-banner mb-5">
        <!-- Photo d'arrière-plan micro cinématographique en fondu -->
        <img src="<?= e($_ctaSettings['bg_image'] ?? 'assets/images/hero-mic-cinematic.jpg') ?>" alt="Studio d'Enregistrement ENSAD" class="cta-banner-bg-photo">

        <div class="position-relative z-2" style="max-width: 620px;">
            <div class="cta-tag"><?= e($_ctaSettings['tag_text'] ?? 'PARTICIPER AU PROJET') ?></div>
            <h2 class="cta-title-main"><?= e($_ctaSettings['title'] ?? 'Une idée ? Un témoignage ?') ?></h2>
            <div class="cta-subtitle-blue display-6 fw-bold"><?= e($_ctaSettings['subtitle'] ?? 'On en parle !') ?></div>
            <p class="cta-text-body">
                <?= e($_ctaSettings['description'] ?? "Vous êtes étudiant, enseignant ou intervenant à l'ENSAD Casablanca ? Rejoignez nos prochains enregistrements et partagez vos projets, expériences et visions du design contemporain.") ?>
            </p>
            <?php
                $_ctaEmail = $_ctaSettings['contact_email'] ?? 'contact@ensad.ma';
                $_ctaText  = $_ctaSettings['cta_text'] ?? 'Proposer un sujet';
            ?>
            <a href="mailto:<?= e($_ctaEmail) ?>?subject=Proposition%20Podcast%20Micro%20ENSAD" class="btn-cta-white">
                <span class="material-symbols-rounded" style="font-size: 1.15rem;">mic</span>
                <span><?= e($_ctaText) ?></span>
            </a>
        </div>

        <!-- Annotation manuscrite en Caveat -->
        <span class="cta-handwritten-note"><?= e($_ctaSettings['handwritten_note'] ?? 'Ta voix compte !') ?></span>
    </section>

</div>

<script>
// Lecteur Audio Mme Randa (Section Hero)
function toggleRandaHeroAudio() {
    const audio = document.getElementById('audioRandaHero');
    const icon  = document.getElementById('iconRandaHero');
    const pWrap = document.getElementById('progressWrapRandaHero');
    if (!audio) return;

    if (audio.paused) {
        // Mettre en pause le lecteur principal de podcast s'il tourne
        if (window.EnsadPlayer && typeof window.EnsadPlayer.pause === 'function') {
            window.EnsadPlayer.pause();
        }

        if (!audio.src || audio.src === window.location.href || audio.src.endsWith('/')) {
            alert("Aucun fichier audio n'a encore été téléversé pour Mme Randa. Vous pouvez l'ajouter facilement depuis le Panneau Admin.");
            return;
        }

        audio.play().then(() => {
            if (icon) icon.className = 'bi bi-pause-fill fs-4 text-white';
            if (pWrap) pWrap.style.setProperty('display', 'flex', 'important');
        }).catch(err => {
            console.warn('Audio play error:', err);
            alert("Le fichier audio configuré n'a pas pu être lu. Veuillez vérifier le fichier ou le lien dans le panneau d'administration.");
        });
    } else {
        audio.pause();
        if (icon) icon.className = 'bi bi-play-fill fs-4 text-white';
    }
}

function seekRandaHeroAudio(e) {
    const audio = document.getElementById('audioRandaHero');
    if (!audio || !audio.duration) return;
    const rect = e.currentTarget.getBoundingClientRect();
    const pos = (e.clientX - rect.left) / rect.width;
    audio.currentTime = pos * audio.duration;
}

document.addEventListener('DOMContentLoaded', () => {
    const audio = document.getElementById('audioRandaHero');
    const icon  = document.getElementById('iconRandaHero');
    const bar   = document.getElementById('progressBarRandaHero');
    const curT  = document.getElementById('timeRandaHero');
    const durT  = document.getElementById('durationRandaHero');

    if (audio) {
        function fmt(s) {
            if (isNaN(s) || !isFinite(s)) return '00:00';
            const m = Math.floor(s / 60);
            const sec = Math.floor(s % 60);
            return (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
        }

        audio.addEventListener('timeupdate', () => {
            if (!audio.duration) return;
            const pct = (audio.currentTime / audio.duration) * 100;
            if (bar) bar.style.width = pct + '%';
            if (curT) curT.textContent = fmt(audio.currentTime);
        });

        audio.addEventListener('loadedmetadata', () => {
            if (durT) durT.textContent = fmt(audio.duration);
        });

        audio.addEventListener('ended', () => {
            if (icon) icon.className = 'bi bi-play-fill fs-4 text-white';
            if (bar) bar.style.width = '0%';
            if (curT) curT.textContent = '00:00';
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
