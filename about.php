<?php
/**
 * Page À Propos - Micro ENSAD Casablanca
 * Redesign Sombre Cinématique Éditorial
 * Projet Pédagogique DENSAD Bac+5 • Semestre 2
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "À propos — Micro ENSAD • Le Podcast des Étudiants";
$pageDescription = "En savoir plus sur Micro ENSAD, la plateforme officielle de podcasts des étudiants de l'École Nationale Supérieure d'Art et de Design de Mohammedia.";
$pageImage = 'https://micro-ensad.ma/assets/images/hero-mic-cinematic.jpg';
$pageUrl = 'https://micro-ensad.ma/about.php';
$activeNav = 'about';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4 py-lg-5">

    <!-- En-tête Institutionnelle avec 2 Logos -->
    <div class="text-center max-w-3xl mx-auto mb-5">
        <div class="d-flex align-items-center justify-content-center gap-3 mb-3 flex-wrap">
            <img src="assets/images/ensad-logo-white.png" alt="Logo Officiel ENSAD" style="height: 48px; width: auto; object-fit: contain;">
            <div class="nav-brand-divider" style="height: 32px;"></div>
            <div class="d-flex align-items-center gap-2">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #0B1F3A; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                    <img src="assets/images/micro-ensad-icon.svg" alt="Micro ENSAD Icon" width="26" height="26">
                </div>
                <div class="d-flex flex-column text-start lh-1">
                    <span class="font-heading fw-bold text-white" style="font-size: 1.1rem; letter-spacing: 0.5px;">MICRO <span class="text-electric-blue">ENSAD</span></span>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.7px;">PODCAST ÉTUDIANT</span>
                </div>
            </div>
        </div>

        <div class="hero-badge-official mb-2">
            <span class="material-symbols-rounded" style="font-size: 0.95rem;">auto_awesome</span>
            <span>LE PODCAST OFFICIEL DES ÉTUDIANTS</span>
        </div>

        <h1 class="display-5 fw-bold text-white mb-3" style="letter-spacing: -0.02em;">Micro ENSAD Casablanca</h1>
        
        <p class="text-muted mx-auto fs-6" style="max-width: 720px; line-height: 1.8;">
            Espace d'expression, de réflexion et de création sonore réalisé par les étudiants du cycle Bac+5 (DENSAD) de l'<strong>École Nationale Supérieure d'Art et de Design de Casablanca</strong> (Université Hassan II de Casablanca).
        </p>

        <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
            <span class="badge bg-primary px-3 py-1 rounded-pill font-monospace" style="font-size: 0.75rem;">DGI &amp; GDA</span>
            <span class="badge bg-secondary bg-opacity-25 text-white px-3 py-1 rounded-pill font-monospace" style="font-size: 0.75rem;">CYCLE BAC+5 (DENSAD)</span>
            <span class="badge border border-primary text-electric-blue px-3 py-1 rounded-pill font-monospace" style="font-size: 0.75rem;">AUDIO PUR YOUTUBE</span>
        </div>
    </div>

    <!-- Bannière Cadre Académique -->
    <div class="featured-episode-panel p-4 p-md-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(18, 100, 255, 0.15); border: 1px solid rgba(47, 123, 255, 0.35); color: var(--electric-blue-light); font-weight: 700; font-size: 0.78rem;">
                    <span class="material-symbols-rounded" style="font-size: 1.1rem;">school</span>
                    <span>CADRE ACADÉMIQUE &amp; PÉDAGOGIQUE</span>
                </div>
                <h2 class="h3 fw-bold text-white mb-3">Module de Français &amp; Communication</h2>
                <p class="text-muted fs-6 mb-3" style="line-height: 1.8;">
                    Ces podcasts ont été imaginés, scénarisés, enregistrés et montés par les étudiants en deuxième semestre du cycle Bac+5 (DENSAD), issus des deux filières d'excellence :
                </p>
                <ul class="text-muted fs-6 mb-3 ps-3" style="line-height: 1.8;">
                    <li><strong class="text-white">Design Graphique et Interactif (DGI)</strong></li>
                    <li><strong class="text-white">Game Design et Animation (GDA)</strong></li>
                </ul>
                <p class="text-muted fs-6 mb-0" style="line-height: 1.8;">
                    Ce projet transversal a été mené dans le cadre du module de français, sous l'encadrement attentif de <strong class="text-white">Madame Randa El Amraoui</strong>, avec pour objectif d'allier éloquence, esprit critique, rigueur conceptuelle et créativité sonore.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-center">
                <div class="p-4 rounded-4" style="background: var(--bg-surface-elevated); border: 1px solid var(--border-color);">
                    <div class="stat-huge-number mb-1">11</div>
                    <h3 class="h6 text-white fw-bold mb-1">Épisodes en Ligne</h3>
                    <p class="small text-muted mb-0">ENSAD Casablanca • Université Hassan II</p>
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-10 small text-muted">
                        Année Universitaire 2025/2026
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Piliers Thématiques -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="p-4 rounded-4 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <span class="material-symbols-rounded fs-2 text-primary mb-3">palette</span>
                <h3 class="h6 fw-bold text-white mb-2">Art &amp; Performance</h3>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Provoc'Art, Artefactum et Débat d'Art : questionner les limites esthétiques, la violence médiatique et l'art contemporain.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="p-4 rounded-4 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <span class="material-symbols-rounded fs-2 text-warning mb-3">sports_esports</span>
                <h3 class="h6 fw-bold text-white mb-2">Jeux Vidéo &amp; Animation</h3>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    ARCADIA et Gaming Culture Maroc : de la conception d'un jeu vidéo aux enjeux d'identité culturelle marocaine.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="p-4 rounded-4 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <span class="material-symbols-rounded fs-2 text-info mb-3">memory</span>
                <h3 class="h6 fw-bold text-white mb-2">Technologie &amp; IA</h3>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Art.exe : explorer l'intelligence artificielle comme outil créatif ou menace potentielle pour les futurs designers.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="p-4 rounded-4 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                <span class="material-symbols-rounded fs-2 text-primary mb-3">work</span>
                <h3 class="h6 fw-bold text-white mb-2">Carrière &amp; Société</h3>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    CTRL+Z, Backstage Pro, Design &amp; Sport : retours d'expériences de stages, insertion pro et design graphique au Maroc.
                </p>
            </div>
        </div>
    </div>

    <!-- Call to action -->
    <div class="cta-cinematic-banner text-center mb-4">
        <h2 class="cta-title-main mb-2">Découvrez Toutes les Émissions</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
            L'ensemble des podcasts des étudiants est disponible en streaming audio haute fidélité.
        </p>
        <a href="episodes.php" class="btn-cta-electric">
            <span class="material-symbols-rounded">podcasts</span>
            <span>Explorer les Épisodes</span>
        </a>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
