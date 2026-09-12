<?php
/**
 * Admin — Éditeur de la Page d'Accueil (Home Editor)
 * Micro ENSAD Mohammedia
 * Modifie le contenu du Hero, CTA Banner, Réseaux Sociaux, Footer et Navbar
 * Sauvegarde dans data/site-settings.json (architecture 100% statique)
 */
require_once __DIR__ . '/auth.php';

$pageTitle = "Éditeur de la Page d'Accueil";
$activeNav = 'home-editor';

// Load settings
$settingsFile = __DIR__ . '/../data/site-settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $raw = file_get_contents($settingsFile);
    $settings = json_decode($raw, true) ?? [];
}

// Flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Shorthand helpers
$welcome = $settings['welcome_page'] ?? [];
$randa   = $settings['randa_intro']   ?? [];
$hero    = $settings['hero']         ?? [];
$cta     = $settings['cta_banner']   ?? [];
$social  = $settings['social']     ?? [];
$footer  = $settings['footer']     ?? [];
$navbar  = $settings['navbar']     ?? [];

function sv(array $arr, string $key, string $default = ''): string {
    return htmlspecialchars($arr[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/layout-header.php';
?>

<?php if ($flash): ?>
    <div class="admin-alert <?= esc($flash['type']) ?>">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= esc($flash['msg']) ?>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
            <i class="bi bi-house-gear me-2 text-primary"></i>Éditeur de la Page d'Accueil
        </h4>
        <small class="text-muted">Toutes les modifications sont enregistrées dans <code>data/site-settings.json</code></small>
    </div>
    <div class="d-flex gap-2">
        <a href="../welcome.php?preview=1" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
            <i class="bi bi-play-btn me-1"></i>Page de Bienvenue
        </a>
        <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="bi bi-eye me-1"></i>Voir le site
        </a>
    </div>
</div>

<form method="POST" action="save-settings.php" id="homeEditorForm" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">

    <!-- ── ACCORDÉON GLOBAL ─────────────────── -->
    <div class="accordion" id="homeEditorAccordion">

        <!-- 0. WELCOME SPLASH PAGE -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#1264FF44!important;box-shadow:0 4px 16px rgba(18,100,255,0.06);">
            <h2 class="accordion-header">
                <button class="accordion-button rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionWelcome"
                        style="background:linear-gradient(135deg,#071426,#0B1F3A);color:#FFFFFF;font-family:'Fredoka',sans-serif;font-size:1.02rem;">
                    <i class="bi bi-play-btn-fill me-2 text-primary" style="color:#2F7BFF!important;"></i>
                    Page d'Accueil &amp; de Bienvenue (Welcome Splash Page)
                    <?php if (!empty($welcome['enabled'])): ?>
                        <span class="badge bg-success ms-2 rounded-pill px-2 py-1 small"><i class="bi bi-check-circle me-1"></i>Activée</span>
                    <?php else: ?>
                        <span class="badge bg-secondary ms-2 rounded-pill px-2 py-1 small">Désactivée</span>
                    <?php endif; ?>
                </button>
            </h2>
            <div id="sectionWelcome" class="accordion-collapse collapse show">
                <div class="accordion-body p-4" style="background:#FAFDFD;">
                    
                    <!-- Toggle & Actions -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded-3 mb-4"
                         style="background:linear-gradient(135deg,#EBF4FE,#F4F8FD);border:1px solid #C8DFF9;">
                        <div class="form-check form-switch mb-2 mb-md-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="welcome_enabled" name="welcome_enabled"
                                   <?= !empty($welcome['enabled']) ? 'checked' : '' ?> style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-bold ms-2" for="welcome_enabled" style="color:#092A5C;">
                                Activer la Page de Bienvenue (Splash Page)
                            </label>
                            <div class="small text-muted">Affiche cette page cinématique avant d'accéder au site principal.</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="form-check form-switch me-2">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="welcome_allow_skip" name="welcome_allow_skip"
                                       <?= !empty($welcome['allow_skip']) ? 'checked' : '' ?>>
                                <label class="form-check-label small text-muted" for="welcome_allow_skip">Bouton « Passer »</label>
                            </div>
                            <a href="../welcome.php?preview=1" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Prévisualiser en direct
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- 1. LIEN VIDÉO -->
                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-white" style="border-color:#DDE9F8!important;">
                                <label class="form-label fw-bold small text-uppercase" style="color:#092A5C;letter-spacing:0.05em;">
                                    <i class="bi bi-youtube text-danger me-1"></i>Lien de la Vidéo (YouTube ou URL directe MP4)
                                </label>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-link-45deg"></i></span>
                                    <input type="text" name="welcome_video_url" id="welcome_video_url" class="form-control"
                                           value="<?= sv($welcome,'video_url','https://www.youtube.com/watch?v=CBif5ouIXAg') ?>"
                                           placeholder="https://www.youtube.com/watch?v=... ou lien .mp4">
                                </div>
                                <div class="form-text small">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Collez n'importe quel lien YouTube (ex: <code>https://www.youtube.com/watch?v=CBif5ouIXAg</code> ou <code>https://youtu.be/...</code>) ou un lien direct MP4. La vidéo sera intégrée dans un lecteur cinématique 16:9 haute définition.
                                </div>
                            </div>
                        </div>

                        <!-- 2. AFFICHE / BANNIÈRE HORIZONTALE -->
                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-white" style="border-color:#DDE9F8!important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold small text-uppercase mb-0" style="color:#092A5C;letter-spacing:0.05em;">
                                        <i class="bi bi-card-image text-primary me-1"></i>Affiche / Bannière Horizontale (الملصق العرضي)
                                    </label>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                        Format Panoramique Recommandé (16:9 ou 21:9)
                                    </span>
                                </div>
                                <p class="small text-muted mb-3">
                                    Cette affiche horizontale est mise en valeur sur la page d'accueil avec bordures lumineuses, typographies soignées et logos officiels.
                                </p>

                                <div class="row g-3 align-items-center">
                                    <!-- Visual Preview Box -->
                                    <div class="col-12 col-md-5">
                                        <?php 
                                        $posterImg = $welcome['poster_image'] ?? 'assets/images/HeaderBG.png';
                                        $posterSrc = (str_starts_with($posterImg, 'http')) ? $posterImg : '../' . ltrim($posterImg, '/');
                                        ?>
                                        <div class="position-relative rounded-3 overflow-hidden border shadow-sm"
                                             style="background:#071426;aspect-ratio:16/9;border-color:#DDE9F8!important;">
                                            <img id="welcome_poster_preview" src="<?= esc($posterSrc) ?>" 
                                                 alt="Aperçu Affiche Horizontale"
                                                 style="width:100%;height:100%;object-fit:cover;display:block;"
                                                 onerror="this.src='../assets/images/HeaderBG.png';">
                                            <div class="position-absolute bottom-0 start-0 end-0 p-2"
                                                 style="background:linear-gradient(to top, rgba(5,11,20,0.9), transparent);">
                                                <small class="text-white fw-bold d-block text-truncate" style="font-size:0.75rem;">
                                                    <i class="bi bi-eye me-1 text-primary"></i>Aperçu en direct du ملصق عرضي
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Upload & Path Inputs -->
                                    <div class="col-12 col-md-7">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">
                                                <i class="bi bi-cloud-arrow-up me-1"></i>Téléverser une nouvelle affiche depuis votre ordinateur :
                                            </label>
                                            <input type="file" name="welcome_poster_file" id="welcome_poster_file" 
                                                   class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                                            <div class="form-text small">JPG, PNG, WebP acceptés. Enregistrement automatique dans <code>assets/images/</code>.</div>
                                        </div>

                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">
                                                <i class="bi bi-folder2-open me-1"></i>Ou spécifier le chemin / URL de l'affiche :
                                            </label>
                                            <input type="text" name="welcome_poster_url" id="welcome_poster_url" 
                                                   class="form-control form-control-sm"
                                                   value="<?= sv($welcome,'poster_image','assets/images/HeaderBG.png') ?>"
                                                   placeholder="assets/images/HeaderBG.png">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. TEXTES & BOUTON START -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Badge Supérieur</label>
                            <input type="text" name="welcome_badge_text" class="form-control form-control-sm"
                                   value="<?= sv($welcome,'badge_text','EXPÉRIENCE IMMERSIVE & AUDIOVISUELLE') ?>"
                                   placeholder="EXPÉRIENCE IMMERSIVE">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Titre d'Accueil</label>
                            <input type="text" name="welcome_title" class="form-control form-control-sm"
                                   value="<?= sv($welcome,'title','Bienvenue sur Micro ENSAD') ?>"
                                   placeholder="Bienvenue sur Micro ENSAD">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Sous-titre d'Accueil</label>
                            <input type="text" name="welcome_subtitle" class="form-control form-control-sm"
                                   value="<?= sv($welcome,'subtitle','La Voix Créative de l\'ENSAD Casablanca') ?>"
                                   placeholder="La Voix Créative de l'ENSAD Casablanca">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold small" style="color:#334155;">Texte descriptif d'introduction</label>
                            <textarea name="welcome_description" class="form-control form-control-sm" rows="2"
                                      placeholder="Présentation immersive..."><?= sv($welcome,'description') ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">
                                <i class="bi bi-play-circle-fill text-primary me-1"></i>Texte du Bouton Start (زر الستارت)
                            </label>
                            <input type="text" name="welcome_cta_start_text" class="form-control form-control-sm fw-bold"
                                   value="<?= sv($welcome,'cta_start_text','Commencer l\'expérience') ?>"
                                   placeholder="Commencer l'expérience">
                            <div class="form-text small">Bouton interactif qui ouvre la plateforme principale.</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- 1. MOT AUDIO DE PRÉSENTATION — MME RANDA EL AMRAOUI -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#1264FF33!important;box-shadow:0 4px 16px rgba(18,100,255,0.06);">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionRanda"
                        style="background:linear-gradient(135deg,#F0F7FF,#EBF4FE);color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-mic-fill me-2 text-primary"></i>
                    Mot Audio de Présentation — Mme Randa El Amraoui (Responsable du Projet)
                    <?php if (!empty($randa['enabled'])): ?>
                        <span class="badge bg-success ms-2 rounded-pill px-2 py-1 small">Activé</span>
                    <?php else: ?>
                        <span class="badge bg-secondary ms-2 rounded-pill px-2 py-1 small">Désactivé</span>
                    <?php endif; ?>
                </button>
            </h2>
            <div id="sectionRanda" class="accordion-collapse collapse">
                <div class="accordion-body p-4" style="background:#FAFDFD;">
                    
                    <!-- Toggle & Options -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded-3 mb-4"
                         style="background:#FFFFFF;border:1px solid #C8DFF9;">
                        <div class="form-check form-switch mb-2 mb-md-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="randa_enabled" name="randa_enabled"
                                   <?= !empty($randa['enabled']) ? 'checked' : '' ?> style="width:2.5em;height:1.3em;">
                            <label class="form-check-label fw-bold ms-2" for="randa_enabled" style="color:#092A5C;">
                                Activer le mot audio de Mme Randa
                            </label>
                            <div class="small text-muted">Permet à Mme Randa de se présenter et d'introduire le projet et les étudiants.</div>
                        </div>

                        <div class="d-flex gap-3 align-items-center flex-wrap">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="randa_show_welcome" name="randa_show_welcome"
                                       <?= !empty($randa['show_in_welcome']) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-secondary" for="randa_show_welcome">
                                    Afficher dans la Page de Bienvenue
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="randa_show_hero" name="randa_show_hero"
                                       <?= !empty($randa['show_in_hero']) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-secondary" for="randa_show_hero">
                                    Afficher dans la Section Hero
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Nom &amp; Prénom de l'enseignante</label>
                            <input type="text" name="randa_name" class="form-control form-control-sm"
                                   value="<?= sv($randa,'name','Mme Randa El Amraoui') ?>"
                                   placeholder="Mme Randa El Amraoui">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Rôle / Titre officiel</label>
                            <input type="text" name="randa_role" class="form-control form-control-sm"
                                   value="<?= sv($randa,'role','Responsable du Projet • Module de Français (Bac+5)') ?>"
                                   placeholder="Responsable du Projet • Module de Français">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small" style="color:#334155;">Titre de l'intervention audio</label>
                            <input type="text" name="randa_title" class="form-control form-control-sm"
                                   value="<?= sv($randa,'title','Mot de Présentation du Projet') ?>"
                                   placeholder="Mot de Présentation du Projet">
                        </div>

                        <!-- FICHIER AUDIO -->
                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-white" style="border-color:#DDE9F8!important;">
                                <label class="form-label fw-bold small text-uppercase mb-2" style="color:#092A5C;">
                                    <i class="bi bi-music-note-beamed text-primary me-1"></i>Fichier Audio du Mot de Présentation (MP3 / WAV / M4A)
                                </label>
                                
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">
                                            <i class="bi bi-cloud-arrow-up me-1"></i>Téléverser l'audio depuis votre appareil :
                                        </label>
                                        <input type="file" name="randa_audio_file" class="form-control form-control-sm" accept="audio/*">
                                        <div class="form-text small">MP3, WAV, M4A acceptés. Enregistrement direct dans <code>assets/audio/</code>.</div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">
                                            <i class="bi bi-link-45deg me-1"></i>Ou saisir le chemin / URL de l'audio :
                                        </label>
                                        <input type="text" name="randa_audio_url" class="form-control form-control-sm"
                                               value="<?= sv($randa,'audio_url') ?>"
                                               placeholder="assets/audio/mot-randa.mp3 ou URL">
                                    </div>

                                    <?php if (!empty($randa['audio_url'])): ?>
                                        <div class="col-12 mt-2">
                                            <label class="form-label small text-muted mb-1"><i class="bi bi-volume-up me-1"></i>Lecteur de test :</label>
                                            <audio controls class="w-100" style="height:36px;">
                                                <source src="../<?= ltrim($randa['audio_url'], '/') ?>">
                                                Votre navigateur ne supporte pas la lecture audio.
                                            </audio>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- PHOTO / AVATAR & CITATION -->
                        <div class="col-md-5">
                            <div class="p-3 rounded-3 border bg-white h-100" style="border-color:#DDE9F8!important;">
                                <label class="form-label fw-bold small text-uppercase mb-2" style="color:#092A5C;">
                                    <i class="bi bi-person-bounding-box text-primary me-1"></i>Photo / Avatar
                                </label>
                                
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <?php 
                                    $avatar = $randa['avatar_image'] ?? 'assets/images/micro-ensad-icon.svg';
                                    $avatarSrc = (str_starts_with($avatar, 'http')) ? $avatar : '../' . ltrim($avatar, '/');
                                    ?>
                                    <img src="<?= esc($avatarSrc) ?>" alt="Avatar Mme Randa" 
                                         class="rounded-circle border shadow-sm"
                                         style="width:54px;height:54px;object-fit:cover;background:#092A5C;">
                                    <div class="flex-grow-1">
                                        <input type="file" name="randa_avatar_file" class="form-control form-control-sm mb-1" accept="image/*">
                                        <input type="text" name="randa_avatar_url" class="form-control form-control-sm"
                                               value="<?= sv($randa,'avatar_image','assets/images/micro-ensad-icon.svg') ?>"
                                               placeholder="assets/images/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="p-3 rounded-3 border bg-white h-100" style="border-color:#DDE9F8!important;">
                                <label class="form-label fw-bold small text-uppercase mb-2" style="color:#092A5C;">
                                    <i class="bi bi-chat-quote text-primary me-1"></i>Texte d'accompagnement / Citation
                                </label>
                                <textarea name="randa_quote" class="form-control form-control-sm" rows="3"
                                          placeholder="Bienvenue sur Micro ENSAD ! Découvrez les créations sonores de nos étudiants..."><?= sv($randa,'quote') ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- 2. HERO SECTION -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionHero"
                        style="background:#EBF4FE;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-stars me-2 text-primary"></i>Section Hero (En-tête principal)
                </button>
            </h2>
            <div id="sectionHero" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small" style="color:#334155;">Badge Officiel</label>
                            <input type="text" name="hero_badge_text" class="form-control form-control-sm"
                                   value="<?= sv($hero,'badge_text','LE PODCAST OFFICIEL DE L\'ENSAD') ?>"
                                   placeholder="LE PODCAST OFFICIEL DE L'ENSAD">
                            <div class="form-text">Texte de la petite pastille en haut du Hero.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Titre Principal</label>
                            <input type="text" name="hero_title" class="form-control form-control-sm"
                                   value="<?= sv($hero,'title','Micro ENSAD') ?>"
                                   placeholder="Micro ENSAD">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Sous-titre</label>
                            <input type="text" name="hero_subtitle" class="form-control form-control-sm"
                                   value="<?= sv($hero,'subtitle','Le Podcast des Étudiants') ?>"
                                   placeholder="Le Podcast des Étudiants">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" style="color:#334155;">Description / Accroche</label>
                            <textarea name="hero_description" class="form-control form-control-sm" rows="3"
                                      placeholder="Description immersive..."><?= sv($hero,'description') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Bouton « Écouter »</label>
                            <input type="text" name="hero_cta_listen" class="form-control form-control-sm"
                                   value="<?= sv($hero,'cta_listen_text','Écouter le dernier épisode') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Bouton « Découvrir »</label>
                            <input type="text" name="hero_cta_discover" class="form-control form-control-sm"
                                   value="<?= sv($hero,'cta_discover_text','Découvrir les épisodes') ?>">
                        </div>
                    </div>
                    <div class="mt-3 p-3 rounded-3" style="background:#F0F7FF;border:1px solid #DDE9F8;">
                        <small class="text-muted"><i class="bi bi-info-circle me-1 text-primary"></i>
                        Pour changer l'image du micro (Hero) ou les Logos, utilisez les sections dédiées ci-dessous.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. LOGOS -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionLogos"
                        style="background:#F0F7FF;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-bezier2 me-2 text-primary"></i>Logos (Principal &amp; Secondaire)
                </button>
            </h2>
            <div id="sectionLogos" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <div class="row g-4">
                        <?php
                        $logoTypes = [
                            'principal'  => ['label' => 'Logo Principal', 'file' => 'logo-principal.svg', 'desc' => 'Affiché dans la Navbar et le Footer'],
                            'secondaire' => ['label' => 'Logo Secondaire', 'file' => 'logo-secondaire.svg', 'desc' => 'Affiché dans la carte Story QR et l\'en-tête'],
                        ];
                        foreach ($logoTypes as $type => $info):
                            $logoPath = __DIR__ . '/../assets/images/' . $info['file'];
                            $logoExists = file_exists($logoPath);
                        ?>
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 border h-100" style="border-color:#DDE9F8!important;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-image text-primary"></i>
                                    <div>
                                        <div class="fw-semibold small" style="color:#092A5C;"><?= esc($info['label']) ?></div>
                                        <small class="text-muted"><?= esc($info['desc']) ?></small>
                                    </div>
                                </div>
                                <!-- Preview area -->
                                <div class="d-flex gap-3 mb-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                                         style="background:#092A5C;width:100px;height:60px;flex-shrink:0;padding:8px;">
                                        <?php if ($logoExists): ?>
                                            <img src="../assets/images/<?= esc($info['file']) ?>?v=<?= filemtime($logoPath) ?>" 
                                                 alt="<?= esc($info['label']) ?>" style="max-width:100%;max-height:100%;object-fit:contain;">
                                        <?php else: ?>
                                            <span class="text-white-50 small">Aucun logo</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rounded-3 d-flex align-items-center justify-content-center border" 
                                         style="background:#FFFFFF;width:100px;height:60px;flex-shrink:0;padding:8px;">
                                        <?php if ($logoExists): ?>
                                            <img src="../assets/images/<?= esc($info['file']) ?>?v=<?= filemtime($logoPath) ?>" 
                                                 alt="<?= esc($info['label']) ?>" style="max-width:100%;max-height:100%;object-fit:contain;">
                                        <?php else: ?>
                                            <span class="text-muted small">Aucun logo</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a href="logos.php" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-upload me-1"></i>Gérer ce logo (SVG)
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 p-3 rounded-3" style="background:#FFF8E1;border:1px solid #FFE082;">
                        <small class="text-warning-emphasis"><i class="bi bi-exclamation-triangle me-1"></i>
                        La gestion des fichiers SVG se fait via la page <strong>Logos SVG</strong> pour garantir la validation du format vectoriel.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. NAVBAR -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionNavbar"
                        style="background:#F0F7FF;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-layout-text-window-reverse me-2 text-primary"></i>Barre de Navigation
                </button>
            </h2>
            <div id="sectionNavbar" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Titre de marque</label>
                            <input type="text" name="navbar_brand_title" class="form-control form-control-sm"
                                   value="<?= sv($navbar,'brand_title','Micro ENSAD') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Sous-titre de marque</label>
                            <input type="text" name="navbar_brand_subtitle" class="form-control form-control-sm"
                                   value="<?= sv($navbar,'brand_subtitle','Le Podcast des Étudiants') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Bouton CTA Écouter</label>
                            <input type="text" name="navbar_listen_btn" class="form-control form-control-sm"
                                   value="<?= sv($navbar,'listen_btn_text','Écouter') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. RÉSEAUX SOCIAUX -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionSocial"
                        style="background:#F0F7FF;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-share me-2 text-primary"></i>Icônes &amp; Liens Réseaux Sociaux
                </button>
            </h2>
            <div id="sectionSocial" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Activez ou désactivez chaque réseau et mettez à jour les URLs. Les réseaux désactivés n'apparaîtront pas dans le footer du site.
                    </p>
                    <div class="row g-3">
                        <?php
                        $socialMeta = [
                            'instagram' => ['icon' => 'bi-instagram',    'color' => '#E1306C', 'name' => 'Instagram'],
                            'youtube'   => ['icon' => 'bi-youtube',      'color' => '#FF0000', 'name' => 'YouTube'],
                            'linkedin'  => ['icon' => 'bi-linkedin',     'color' => '#0077B5', 'name' => 'LinkedIn'],
                            'spotify'   => ['icon' => 'bi-spotify',      'color' => '#1DB954', 'name' => 'Spotify'],
                            'facebook'  => ['icon' => 'bi-facebook',     'color' => '#1877F2', 'name' => 'Facebook'],
                            'tiktok'    => ['icon' => 'bi-tiktok',       'color' => '#000000', 'name' => 'TikTok'],
                        ];
                        foreach ($socialMeta as $key => $meta):
                            $s = $social[$key] ?? ['url' => '', 'enabled' => false, 'label' => $meta['name']];
                        ?>
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 border" style="border-color:#EDF2F7;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="background:<?= $meta['color'] ?>22;width:36px;height:36px;flex-shrink:0;">
                                        <i class="bi <?= $meta['icon'] ?>" style="color:<?= $meta['color'] ?>;font-size:1.1rem;"></i>
                                    </div>
                                    <div class="fw-semibold small" style="color:#092A5C;"><?= $meta['name'] ?></div>
                                    <div class="ms-auto form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="social_<?= $key ?>_enabled"
                                               name="social_<?= $key ?>_enabled"
                                               <?= !empty($s['enabled']) ? 'checked' : '' ?>>
                                        <label class="form-check-label small text-muted" for="social_<?= $key ?>_enabled">Activé</label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">URL</label>
                                    <input type="url" name="social_<?= $key ?>_url" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($s['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://<?= $key ?>.com/votre-compte">
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Libellé (aria-label)</label>
                                    <input type="text" name="social_<?= $key ?>_label" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($s['label'] ?? $meta['name'], ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="<?= $meta['name'] ?> Micro ENSAD">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. BANNIÈRE CTA -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionCta"
                        style="background:#F0F7FF;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-megaphone me-2 text-primary"></i>Bannière CTA (« Participer »)
                </button>
            </h2>
            <div id="sectionCta" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Étiquette Tag</label>
                            <input type="text" name="cta_tag_text" class="form-control form-control-sm"
                                   value="<?= sv($cta,'tag_text','PARTICIPER AU PROJET') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Titre</label>
                            <input type="text" name="cta_title" class="form-control form-control-sm"
                                   value="<?= sv($cta,'title','Une idée ? Un témoignage ?') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Sous-titre (bleu)</label>
                            <input type="text" name="cta_subtitle" class="form-control form-control-sm"
                                   value="<?= sv($cta,'subtitle','On en parle !') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" style="color:#334155;">Description</label>
                            <textarea name="cta_description" class="form-control form-control-sm" rows="2"
                                      placeholder="Texte descriptif de la bannière..."><?= sv($cta,'description') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Texte du bouton</label>
                            <input type="text" name="cta_cta_text" class="form-control form-control-sm"
                                   value="<?= sv($cta,'cta_text','Proposer un sujet') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Email de contact</label>
                            <input type="email" name="cta_contact_email" class="form-control form-control-sm"
                                   value="<?= sv($cta,'contact_email','contact@ensad.ma') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" style="color:#334155;">Note manuscrite (Caveat)</label>
                            <input type="text" name="cta_handwritten_note" class="form-control form-control-sm"
                                   value="<?= sv($cta,'handwritten_note','Ta voix compte !') ?>"
                                   placeholder="Ta voix compte !">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. FOOTER -->
        <div class="accordion-item border rounded-3 mb-3" style="border-color:#DDE9F8!important;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#sectionFooter"
                        style="background:#F0F7FF;color:#092A5C;font-family:'Fredoka',sans-serif;font-size:1rem;">
                    <i class="bi bi-layout-text-sidebar-reverse me-2 text-primary"></i>Pied de Page (Footer)
                </button>
            </h2>
            <div id="sectionFooter" class="accordion-collapse collapse">
                <div class="accordion-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Nom de l'institution</label>
                            <input type="text" name="footer_institution" class="form-control form-control-sm"
                                   value="<?= sv($footer,'institution_name') ?>"
                                   placeholder="École Nationale Supérieure d'Art et de Design de Casablanca">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Nom de l'université</label>
                            <input type="text" name="footer_university" class="form-control form-control-sm"
                                   value="<?= sv($footer,'university_name') ?>"
                                   placeholder="Université Hassan II de Casablanca">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Slogan du podcast</label>
                            <input type="text" name="footer_tagline" class="form-control form-control-sm"
                                   value="<?= sv($footer,'podcast_tagline','Le Podcast des Étudiants') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color:#334155;">Nom copyright</label>
                            <input type="text" name="footer_copyright" class="form-control form-control-sm"
                                   value="<?= sv($footer,'copyright_name','Micro ENSAD') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /accordion -->

    <!-- SUBMIT BAR -->
    <div class="d-flex align-items-center justify-content-between p-4 rounded-3 mt-2"
         style="background:linear-gradient(135deg,#EBF4FE,#F0F7FF);border:2px solid #DDE9F8;">
        <div>
            <div class="fw-bold" style="color:#092A5C;font-family:'Fredoka',sans-serif;">Prêt à enregistrer ?</div>
            <small class="text-muted">Les modifications seront visibles immédiatement sur le site public.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-eye me-1"></i>Prévisualiser
            </a>
            <button type="submit" class="btn-admin-primary px-4 py-2 rounded-pill fw-bold">
                <i class="bi bi-floppy me-2"></i>Enregistrer toutes les modifications
            </button>
        </div>
    </div>

</form>

<script>
// Prévisualisation instantanée de l'affiche horizontale
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('welcome_poster_file');
    const urlInput  = document.getElementById('welcome_poster_url');
    const previewImg = document.getElementById('welcome_poster_preview');

    if (fileInput && previewImg) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (urlInput && previewImg) {
        urlInput.addEventListener('input', function() {
            const val = this.value.trim();
            if (val) {
                previewImg.src = val.startsWith('http') ? val : ('../' + val.replace(/^\//, ''));
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>