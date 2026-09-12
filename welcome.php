<?php
/**
 * Page d'Accueil & de Bienvenue — Micro ENSAD Casablanca
 * Welcome Splash Modal avec :
 * - Arrière-plan cinématique avec l'image floutée (Blur effect)
 * - Fenêtre modale / Écran pop-up flottant central en verre (Glassmorphism)
 * - Les deux logos officiels (ENSAD à gauche, Université Hassan II à droite)
 * - Vidéo intégrée directe en 16:9
 * - Mot de Présentation Audio de Mme Randa El Amraoui (Responsable du Projet)
 * - Bouton « Start » lumineux et interactif pour entrer sur le site
 */
require_once __DIR__ . '/includes/functions.php';

if (!function_exists('esc')) {
    function esc(?string $s): string {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Charger les paramètres personnalisables du site
$settingsFile = __DIR__ . '/data/site-settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
}

$welcome = $settings['welcome_page'] ?? [];
$isWelcomeEnabled = !empty($welcome['enabled']);
$isPreview = isset($_GET['preview']) && $_GET['preview'] == '1';

// Si la page de bienvenue est désactivée et qu'on n'est pas en prévisualisation admin, rediriger vers index.php
if (!$isWelcomeEnabled && !$isPreview) {
    header('Location: index.php?skip_welcome=1');
    exit;
}

// Extraction des données de bienvenue
$badgeText    = $welcome['badge_text']    ?? 'EXPÉRIENCE IMMERSIVE & AUDIOVISUELLE';
$titleText    = $welcome['title']         ?? 'Bienvenue sur Micro ENSAD';
$subtitleText = $welcome['subtitle']      ?? "La Voix Créative de l'ENSAD Casablanca";
$descText     = $welcome['description']   ?? "Découvrez les réflexions, projets et créations des étudiants de l'École Nationale Supérieure d'Art et de Design de Casablanca.";
$videoUrl     = $welcome['video_url']     ?? 'https://www.youtube.com/watch?v=CBif5ouIXAg';
$posterImage  = $welcome['poster_image']  ?? 'assets/images/HeaderBG.png';
$startText    = $welcome['cta_start_text'] ?? "Commencer l'expérience";
$allowSkip    = $welcome['allow_skip']    ?? true;

// Analyse du lien vidéo (YouTube vs Fichier Direct)
$youtubeId = '';
if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
    $youtubeId = $matches[1];
}

// Résolution du chemin de l'affiche d'arrière-plan
$posterSrc = (str_starts_with($posterImage, 'http')) ? $posterImage : ltrim($posterImage, '/');
if (!str_starts_with($posterSrc, 'http') && !file_exists(__DIR__ . '/' . $posterSrc)) {
    $posterSrc = 'assets/images/HeaderBG.png';
}

// Extraction des données de Mme Randa El Amraoui
$randa = $settings['randa_intro'] ?? [];
$isRandaWelcomeEnabled = !empty($randa['enabled']) && !empty($randa['show_in_welcome']);
$randaName   = $randa['name'] ?? 'Mme Randa El Amraoui';
$randaRole   = $randa['role'] ?? 'Responsable du Projet • Module de Français (Bac+5)';
$randaTitle  = $randa['title'] ?? 'Mot de Présentation du Projet';
$randaAudio  = $randa['audio_url'] ?? '';
$randaAvatar = $randa['avatar_image'] ?? 'assets/images/micro-ensad-icon.svg';
$randaQuote  = $randa['quote'] ?? "Bienvenue sur Micro ENSAD ! Je vous invite à découvrir les créations et réflexions de nos étudiants.";

$randaAudioSrc = '';
if (!empty($randaAudio)) {
    $randaAudioSrc = (str_starts_with($randaAudio, 'http')) ? $randaAudio : ltrim($randaAudio, '/');
}
$randaAvatarSrc = (str_starts_with($randaAvatar, 'http')) ? $randaAvatar : ltrim($randaAvatar, '/');
if (!str_starts_with($randaAvatarSrc, 'http') && !file_exists(__DIR__ . '/' . $randaAvatarSrc)) {
    $randaAvatarSrc = 'assets/images/micro-ensad-icon.svg';
}

$pageTitle = e($titleText) . " • Micro ENSAD";
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= e(strip_tags($descText)) ?>">
    <meta name="theme-color" content="#050B14">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/micro-ensad-icon.svg">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Main Style -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Reset et fond de page */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #050B14;
            color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        /* 1. ARRIÈRE-PLAN AVEC L'IMAGE FLOUTÉE (BLUR EFFECT) */
        .welcome-bg-blur {
            position: fixed;
            top: -5%;
            left: -5%;
            width: 110%;
            height: 110%;
            background-image: url('<?= esc($posterSrc) ?>');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            filter: blur(34px) brightness(0.32) saturate(1.35);
            transform: scale(1.06);
            z-index: 0;
            pointer-events: none;
            will-change: transform, filter;
        }
        
        .welcome-bg-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 50% 35%, rgba(18, 100, 255, 0.16) 0%, rgba(5, 11, 20, 0.84) 80%);
            z-index: 1;
            pointer-events: none;
        }

        /* 2. CONTENEUR FLOTTANT CENTRÉ (STYLE POPUP / MODAL) */
        .welcome-modal-viewport {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 16px;
            box-sizing: border-box;
        }

        .welcome-popup-card {
            width: 100%;
            max-width: 860px;
            background: rgba(7, 20, 38, 0.84);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 26px;
            box-shadow: 
                0 30px 80px -10px rgba(0, 0, 0, 0.9),
                0 0 50px rgba(18, 100, 255, 0.24),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
            overflow: hidden;
            animation: popupSpring 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
        }

        @keyframes popupSpring {
            0% {
                opacity: 0;
                transform: scale(0.92) translateY(24px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* En-tête de la modale avec les deux logos */
        .popup-header {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(5, 11, 20, 0.45);
        }
        .popup-logo-img {
            height: 38px;
            max-width: 140px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
            transition: transform 0.25s ease;
        }
        .popup-logo-img:hover {
            transform: scale(1.05);
        }
        .popup-brand-pill {
            background: rgba(18, 100, 255, 0.15);
            border: 1px solid rgba(18, 100, 255, 0.35);
            color: #2F7BFF;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* Corps de la modale */
        .popup-body {
            padding: 22px 26px 26px;
        }

        .popup-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(18, 100, 255, 0.18);
            border: 1px solid rgba(18, 100, 255, 0.4);
            color: #FFFFFF;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }

        .popup-title {
            font-size: clamp(1.4rem, 3.2vw, 2rem);
            font-weight: 800;
            line-height: 1.2;
            color: #FFFFFF;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }
        .popup-subtitle {
            color: #2F7BFF;
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            font-weight: 600;
            margin-bottom: 16px;
        }

        /* 3. LECTEUR VIDÉO DIRECT (16:9) */
        .popup-video-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 */
            border-radius: 18px;
            overflow: hidden;
            background: #000000;
            border: 1px solid rgba(18, 100, 255, 0.35);
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.8),
                0 0 30px rgba(18, 100, 255, 0.2);
            margin-bottom: 18px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .popup-video-wrapper:hover {
            border-color: rgba(47, 123, 255, 0.6);
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.9),
                0 0 45px rgba(18, 100, 255, 0.35);
        }
        .popup-video-wrapper iframe,
        .popup-video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* 4. CARTE AUDIO MME RANDA EL AMRAOUI DANS LA MODALE */
        .popup-randa-card {
            background: rgba(11, 31, 58, 0.72);
            border: 1px solid rgba(18, 100, 255, 0.35);
            border-radius: 18px;
            padding: 14px 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .popup-randa-card:hover {
            border-color: rgba(47, 123, 255, 0.55);
            box-shadow: 0 10px 30px rgba(18, 100, 255, 0.25);
        }
        .popup-randa-avatar {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border: 2px solid #2F7BFF;
            background: #092A5C;
            box-shadow: 0 4px 12px rgba(18, 100, 255, 0.3);
        }
        .popup-randa-mic-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: #1264FF;
            color: #FFFFFF;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            border: 1.5px solid #071426;
        }
        .popup-randa-tag {
            background: rgba(18, 100, 255, 0.22);
            border: 1px solid rgba(18, 100, 255, 0.45);
            color: #2F7BFF;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            letter-spacing: 0.05em;
        }
        .popup-randa-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #FFFFFF;
        }
        .popup-randa-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: #D8E7FA;
        }
        .popup-randa-role {
            font-size: 0.72rem;
            color: #7E94B4;
        }
        .btn-randa-play {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #1264FF, #2F7BFF);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(18, 100, 255, 0.4);
            cursor: pointer;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }
        .btn-randa-play:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(18, 100, 255, 0.6);
        }
        .popup-randa-progress-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .popup-randa-time {
            font-size: 0.72rem;
            color: #7E94B4;
            font-variant-numeric: tabular-nums;
            min-width: 34px;
        }
        .popup-randa-progress-bar {
            flex: 1;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            overflow: hidden;
            cursor: pointer;
            position: relative;
        }
        .popup-randa-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #1264FF, #2F7BFF);
            border-radius: 999px;
            transition: width 0.1s linear;
        }
        .popup-randa-quote {
            font-size: 0.78rem;
            color: #A8B7CC;
            font-style: italic;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 8px;
            line-height: 1.45;
        }

        /* Pied de la modale / Bouton Start */
        .popup-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding-top: 4px;
        }

        .btn-start-action {
            width: 100%;
            max-width: 440px;
            background: linear-gradient(135deg, #1264FF 0%, #2F7BFF 100%);
            color: #FFFFFF !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 0.02em;
            padding: 16px 36px;
            border-radius: 999px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 
                0 10px 30px rgba(18, 100, 255, 0.45),
                0 0 25px rgba(47, 123, 255, 0.35);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-start-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.6s ease;
        }
        .btn-start-action:hover::before {
            left: 100%;
        }
        .btn-start-action:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 
                0 16px 45px rgba(18, 100, 255, 0.6),
                0 0 35px rgba(47, 123, 255, 0.5);
        }
        .btn-start-action:active {
            transform: translateY(-1px) scale(0.99);
        }

        /* Pulsation subtile */
        @keyframes pulseGlow {
            0% { box-shadow: 0 10px 30px rgba(18, 100, 255, 0.45), 0 0 0 0 rgba(18, 100, 255, 0.4); }
            70% { box-shadow: 0 10px 30px rgba(18, 100, 255, 0.45), 0 0 0 14px rgba(18, 100, 255, 0); }
            100% { box-shadow: 0 10px 30px rgba(18, 100, 255, 0.45), 0 0 0 0 rgba(18, 100, 255, 0); }
        }
        .pulse-start-btn {
            animation: pulseGlow 2.8s infinite;
        }

        .btn-skip-link {
            color: #7E94B4;
            font-size: 0.88rem;
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-skip-link:hover {
            color: #FFFFFF;
        }

        /* Mode Prévisualisation Admin */
        .preview-admin-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #FFD500;
            color: #050B14;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 16px;
            text-align: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* Adaptation Écrans Mobiles */
        @media (max-width: 767.98px) {
            .welcome-modal-viewport {
                padding: 16px 10px;
            }
            .welcome-popup-card {
                border-radius: 20px;
            }
            .popup-header {
                padding: 14px 16px;
            }
            .popup-logo-img {
                height: 30px;
                max-width: 100px;
            }
            .popup-brand-pill {
                display: none;
            }
            .popup-body {
                padding: 16px 14px 20px;
            }
            .popup-video-wrapper {
                border-radius: 14px;
                margin-bottom: 14px;
            }
            .popup-randa-card {
                padding: 12px 14px;
                margin-bottom: 16px;
            }
            .popup-randa-avatar {
                width: 44px;
                height: 44px;
            }
            .btn-start-action {
                font-size: 1.05rem;
                padding: 14px 24px;
            }
        }
    </style>
</head>
<body>

    <?php if ($isPreview): ?>
        <div class="preview-admin-bar">
            <i class="bi bi-eye-fill me-1"></i>MODE PRÉVISUALISATION ADMIN — Les cookies d'accès ne sont pas modifiés.
            <a href="admin/home-editor.php" class="text-dark fw-bold ms-2 text-decoration-underline">Retourner à l'éditeur</a>
        </div>
    <?php endif; ?>

    <!-- 1. IMAGE D'ARRIÈRE-PLAN AVEC FLOU ARTISTIQUE (BLUR EFFECT) -->
    <div class="welcome-bg-blur"></div>
    <div class="welcome-bg-overlay"></div>

    <!-- 2. POPUP MODAL FLOTTANT (ÉCRAN POPUP) -->
    <div class="welcome-modal-viewport">
        <div class="welcome-popup-card">

            <!-- En-tête de la modale avec les deux logos -->
            <div class="popup-header">
                <!-- Logo Gauche : ENSAD Casablanca -->
                <div class="d-flex align-items-center">
                    <img src="assets/images/logo-principal.svg" alt="ENSAD Casablanca" class="popup-logo-img">
                </div>

                <!-- Badge Central : Micro ENSAD -->
                <div class="d-none d-md-block">
                    <span class="popup-brand-pill">
                        <i class="bi bi-broadcast me-1"></i>Micro ENSAD • Plateforme Officielle
                    </span>
                </div>

                <!-- Logo Droite : Université Hassan II -->
                <div class="d-flex align-items-center">
                    <img src="assets/images/logo-secondaire.svg" alt="Université Hassan II de Casablanca" class="popup-logo-img">
                </div>
            </div>

            <!-- Corps de la modale -->
            <div class="popup-body">

                <!-- Badge & Titre de bienvenue -->
                <div class="text-center mb-3">
                    <div class="popup-badge">
                        <i class="bi bi-stars" style="color: #FFD500;"></i>
                        <?= e($badgeText) ?>
                    </div>
                    <h1 class="popup-title"><?= e($titleText) ?></h1>
                    <div class="popup-subtitle"><?= e($subtitleText) ?></div>
                </div>

                <!-- 3. LECTEUR VIDÉO DIRECT (16:9) -->
                <div class="popup-video-wrapper">
                    <?php if ($youtubeId): ?>
                        <iframe id="welcomeYtIframe"
                                src="https://www.youtube-nocookie.com/embed/<?= esc($youtubeId) ?>?rel=0&enablejsapi=1&modestbranding=1"
                                title="<?= e($titleText) ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                    <?php elseif (!empty($videoUrl)): ?>
                        <video id="welcomeVideoElement" controls preload="metadata">
                            <source src="<?= esc($videoUrl) ?>" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture de cette vidéo.
                        </video>
                    <?php else: ?>
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-play-circle fs-1"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3.5 MOT DE PRÉSENTATION AUDIO DE MME RANDA EL AMRAOUI -->
                <?php if ($isRandaWelcomeEnabled): ?>
                    <div class="popup-randa-card">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Avatar avec badge micro -->
                            <div class="position-relative flex-shrink-0">
                                <img src="<?= esc($randaAvatarSrc) ?>" alt="<?= e($randaName) ?>" 
                                     class="popup-randa-avatar rounded-circle">
                                <span class="popup-randa-mic-badge">
                                    <i class="bi bi-mic-fill"></i>
                                </span>
                            </div>

                            <!-- Infos -->
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="popup-randa-tag">MOT DE L'ENCADRANTE</span>
                                    <span class="popup-randa-name" style="white-space: normal; line-height: 1.3;"><?= e($randaName) ?></span>
                                </div>
                                <div class="popup-randa-title mt-1" style="white-space: normal; line-height: 1.3;"><?= e($randaTitle) ?></div>
                                <div class="popup-randa-role mt-1" style="white-space: normal; line-height: 1.3;"><?= e($randaRole) ?></div>
                            </div>

                            <!-- Bouton Play/Pause -->
                            <button type="button" id="btnPlayRandaWelcome" class="btn-randa-play" 
                                    onclick="toggleRandaAudio('welcome')" 
                                    title="Écouter la présentation de Mme Randa">
                                <i id="iconRandaWelcome" class="bi bi-play-fill fs-4"></i>
                            </button>
                        </div>

                        <!-- Lecteur Audio HTML5 & Barre de progression -->
                        <audio id="audioRandaWelcome" src="<?= esc($randaAudioSrc) ?>" preload="metadata"></audio>
                        <div id="progressWrapRandaWelcome" class="popup-randa-progress-wrap mt-2">
                            <span id="timeRandaWelcome" class="popup-randa-time">00:00</span>
                            <div class="popup-randa-progress-bar" onclick="seekRandaAudio(event, 'welcome')">
                                <div id="barRandaWelcome" class="popup-randa-progress-fill"></div>
                            </div>
                            <span id="durationRandaWelcome" class="popup-randa-time">--:--</span>
                        </div>

                        <?php if (!empty($randaQuote)): ?>
                            <div class="popup-randa-quote mt-2">
                                « <?= e($randaQuote) ?> »
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- 4. BOUTON START INTERACTIF (زر الستارت) -->
                <div class="popup-footer">
                    <button type="button" id="btnEnterSite" class="btn-start-action pulse-start-btn" onclick="enterSite()">
                        <i class="bi bi-play-circle-fill fs-5"></i>
                        <span><?= e($startText) ?></span>
                        <i class="bi bi-arrow-right fs-5"></i>
                    </button>

                    <?php if ($allowSkip): ?>
                        <a href="javascript:void(0)" onclick="enterSite()" class="btn-skip-link">
                            <span>Accéder directement au site</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

    <!-- Script d'interaction et de redirection avec cookie -->
    <script>
        // Lecteur Audio Mme Randa
        function toggleRandaAudio(context) {
            const audio = document.getElementById(context === 'welcome' ? 'audioRandaWelcome' : 'audioRandaHero');
            const icon  = document.getElementById(context === 'welcome' ? 'iconRandaWelcome' : 'iconRandaHero');
            if (!audio) return;

            if (audio.paused) {
                // Pause de la vidéo si elle est en cours
                const ytIframe = document.getElementById('welcomeYtIframe');
                if (ytIframe && ytIframe.contentWindow) {
                    ytIframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                }
                const vidEl = document.getElementById('welcomeVideoElement');
                if (vidEl && !vidEl.paused) {
                    vidEl.pause();
                }

                if (!audio.src || audio.src === window.location.href || audio.src.endsWith('/')) {
                    alert("Aucun fichier audio n'a encore été téléversé pour Mme Randa. Vous pouvez ajouter le fichier audio (MP3) ou son lien directement dans l'espace Administration.");
                    return;
                }

                audio.play().then(() => {
                    if (icon) icon.className = 'bi bi-pause-fill fs-4';
                }).catch(err => {
                    console.warn('Audio play error:', err);
                    alert("Le fichier audio configuré n'a pas pu être lu. Veuillez vérifier le fichier ou le lien dans le panneau d'administration.");
                });
            } else {
                audio.pause();
                if (icon) icon.className = 'bi bi-play-fill fs-4';
            }
        }

        function seekRandaAudio(e, context) {
            const audio = document.getElementById(context === 'welcome' ? 'audioRandaWelcome' : 'audioRandaHero');
            if (!audio || !audio.duration) return;
            const rect = e.currentTarget.getBoundingClientRect();
            const pos = (e.clientX - rect.left) / rect.width;
            audio.currentTime = pos * audio.duration;
        }

        function formatTime(sec) {
            if (isNaN(sec) || !isFinite(sec)) return '00:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('audioRandaWelcome');
            const icon  = document.getElementById('iconRandaWelcome');
            const bar   = document.getElementById('barRandaWelcome');
            const curT  = document.getElementById('timeRandaWelcome');
            const durT  = document.getElementById('durationRandaWelcome');

            if (audio) {
                audio.addEventListener('timeupdate', () => {
                    if (!audio.duration) return;
                    const pct = (audio.currentTime / audio.duration) * 100;
                    if (bar) bar.style.width = pct + '%';
                    if (curT) curT.textContent = formatTime(audio.currentTime);
                });

                audio.addEventListener('loadedmetadata', () => {
                    if (durT) durT.textContent = formatTime(audio.duration);
                });

                audio.addEventListener('ended', () => {
                    if (icon) icon.className = 'bi bi-play-fill fs-4';
                    if (bar) bar.style.width = '0%';
                    if (curT) curT.textContent = '00:00';
                });
            }
        });

        // Entrer sur le site
        function enterSite() {
            const btn = document.getElementById('btnEnterSite');
            if (btn) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Chargement...';
                btn.style.opacity = '0.85';
                btn.disabled = true;
            }

            try {
                document.cookie = "ensad_welcome_passed=1; path=/; max-age=2592000; SameSite=Lax";
                sessionStorage.setItem('ensad_welcome_passed', '1');
            } catch (e) {
                console.warn('Cookie/Storage error:', e);
            }

            window.location.href = 'index.php?enter=1';
        }
    </script>
</body>
</html>