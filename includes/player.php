<?php
/**
 * Lecteur Audio Persistant - Micro ENSAD Mohammedia
 * 1. Mode Desktop (>= 768px) : Barre flottante avec Dock, Chapitres YouTube, Scrubber et Volume complet.
 * 2. Mode Mobile (< 768px) : Mini-lecteur rétractable + Lecteur Plein Écran Style Spotify (Artwork 1:1, Gros bouton blanc).
 * 3. Tiroir Interactif des Chapitres YouTube & Cache / Téléchargement Hors-Ligne.
 */
?>

<!-- ═════════════════════════════════════════════════════════════════════════ -->
<!-- A. LECTEUR DESKTOP PERSISTANT (Barre Flottante avec Ancrage & Volume)      -->
<!-- ═════════════════════════════════════════════════════════════════════════ -->
<div id="ensadPlayerBar" class="ensad-audio-player-bar dock-center d-none" role="region" aria-label="Lecteur audio de podcast">
    <div class="player-inner-wrap">

        <!-- 1. GAUCHE : COUVERTURE 1:1 AGRANDIE + TITRE & AUTEUR + BOUTON CARTE QR -->
        <div class="player-col-track d-flex align-items-center gap-3">
            <div class="player-cover-square-box position-relative flex-shrink-0">
                <img id="playerCover" src="assets/images/micro-ensad-icon.svg" alt="Pochette de l'épisode" class="player-thumb-square">
                <span class="player-pulse-indicator" title="Audio en cours"></span>
            </div>
            <div class="player-meta-box overflow-hidden">
                <h6 id="playerTitle" class="mb-0 text-truncate font-fredoka fw-bold player-track-title" title="The Next Chapter">The Next Chapter</h6>
                <div class="d-flex align-items-center gap-1">
                    <p id="playerArtist" class="mb-0 text-muted small text-truncate player-track-host">Oussama Laabidate</p>
                </div>
            </div>
            <!-- Bouton Partager / Carte Story & QR Code (Placé naturellement avec l'épisode) -->
            <button type="button" class="player-btn-action player-share-track-btn flex-shrink-0 ms-auto" id="playerDesktopShareBtn" title="Générer la Carte de Partage & QR Code" aria-label="Partager l'épisode">
                <span class="material-symbols-rounded">qr_code_2</span>
            </button>
        </div>

        <!-- 2. CENTRE : CONTRÔLES AUDIO DE LECTURE & SCRUBBER TIMELINE -->
        <div class="player-col-controls d-flex flex-column align-items-center">
            <!-- Ligne de transport (Skip 15s arriere, Play/Pause principal, Skip 15s avant, Egaliseur) -->
            <div class="player-transport-row d-flex align-items-center justify-content-center gap-3 mb-1">
                <!-- Reculer 15 secondes -->
                <button type="button" class="player-btn-action" id="playerBack15" title="Reculer de 15 secondes" aria-label="Reculer de 15 secondes">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="player-svg-icon" aria-hidden="true">
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L2 9"></path>
                        <polyline points="2 4 2 9 7 9"></polyline>
                        <text x="12" y="15.5" font-size="7.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none" font-family="'Montserrat', sans-serif">15</text>
                    </svg>
                </button>

                <!-- Bouton Principal Lecture / Pause -->
                <button type="button" class="player-play-btn-circle" id="playerPlayBtn" title="Lecture / Pause" aria-label="Lecture ou pause">
                    <span class="material-symbols-rounded" id="playerPlayIcon">play_arrow</span>
                </button>

                <!-- Avancer 15 secondes -->
                <button type="button" class="player-btn-action" id="playerFwd15" title="Avancer de 15 secondes" aria-label="Avancer de 15 secondes">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="player-svg-icon" aria-hidden="true">
                        <path d="M20.49 15a9 9 0 1 1-2.13-9.36L22 9"></path>
                        <polyline points="22 4 22 9 17 9"></polyline>
                        <text x="12" y="15.5" font-size="7.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none" font-family="'Montserrat', sans-serif">15</text>
                    </svg>
                </button>

                <!-- Égaliseur d'ondes sonores discrètes -->
                <div class="equalizer-wave d-none d-md-inline-flex ms-1" id="playerWaveBars" title="Lecture audio active">
                    <span class="equalizer-bar"></span>
                    <span class="equalizer-bar"></span>
                    <span class="equalizer-bar"></span>
                    <span class="equalizer-bar"></span>
                </div>
            </div>

            <!-- Ligne de chapitre actif en direct (Style YouTube Chapters épuré & cliquable pour tiroir) -->
            <div class="player-chapter-header d-flex align-items-center justify-content-center w-100 mb-1 d-none" id="playerChapterHeader">
                <button type="button" class="player-chapter-badge-wrap d-inline-flex align-items-center gap-2 px-2.5 py-1 rounded-pill btn-reset" id="btnOpenDesktopChapters" title="Afficher tous les chapitres">
                    <span class="badge-chapter-indicator flex-shrink-0">
                        <span class="material-symbols-rounded" style="font-size: 0.75rem;">bookmark</span>
                        <span id="playerChapterPillNum">Partie 1</span>
                    </span>
                    <span id="playerCurrentChapterTitle" class="text-truncate player-chapter-title-text small fw-medium"></span>
                    <span class="material-symbols-rounded text-muted flex-shrink-0" style="font-size: 0.85rem;">expand_less</span>
                </button>
            </div>

            <!-- Ligne de progression du temps & barre de défilement segmented -->
            <div class="player-timeline-row w-100 d-flex align-items-center gap-2 position-relative">
                <span id="playerCurrentTime" class="font-monospace text-muted small player-time" style="font-size: 0.75rem; min-width: 38px; text-align: right;">00:00</span>
                
                <!-- Zone interactive avec chapitres découpés (YouTube chapter segments) -->
                <div class="player-timeline-interactive-wrap flex-grow-1 position-relative" id="playerTimelineInteractiveWrap">
                    <!-- Segments de chapitres YouTube en arrière-plan (pointer-events: none) -->
                    <div class="player-chapters-track" id="playerChaptersTrack"></div>

                    <!-- Scrubber range input principal avec curseur blanc visible et fluide -->
                    <input type="range" class="slider-timeline slider-timeline-youtube flex-grow-1" id="playerProgressBar" min="0" max="100" value="0" step="0.1" aria-label="Progression du podcast">
                    
                    <!-- Tooltip YouTube au survol du scrubber -->
                    <div class="player-chapter-hover-tooltip d-none" id="playerChapterTooltip">
                        <div class="tooltip-chapter-num" id="tooltipChapterNum"></div>
                        <div class="tooltip-chapter-title text-truncate" id="tooltipChapterTitle"></div>
                        <div class="tooltip-chapter-time font-monospace" id="tooltipChapterTime"></div>
                    </div>
                </div>

                <span id="playerDuration" class="font-monospace text-muted small player-time" style="font-size: 0.75rem; min-width: 38px; text-align: left;">00:00</span>
            </div>
        </div>

        <!-- 3. DROITE : CONTRÔLE COMPLET DU VOLUME & ANCRAGE INTERACTIF (DOCK) -->
        <div class="player-col-right d-flex align-items-center justify-content-end gap-2 gap-sm-3">
            <!-- Contrôles de volume épurés (Mute + Slider fluide + Pourcentage) -->
            <div class="player-volume-group d-flex align-items-center gap-2">
                <!-- Mute / Unmute -->
                <button type="button" class="player-btn-action" id="playerMuteBtn" title="Couper / Rétablir le son" aria-label="Activer ou couper le son">
                    <span class="material-symbols-rounded" id="playerMuteIcon">volume_up</span>
                </button>

                <!-- Slider de volume -->
                <input type="range" class="slider-timeline slider-volume" id="playerVolumeSlider" min="0" max="100" value="85" aria-label="Niveau de volume">

                <!-- Indicateur textuel de pourcentage -->
                <span id="playerVolText" class="font-monospace fw-semibold small text-muted d-none d-xl-inline" style="font-size: 0.75rem; min-width: 32px;">85%</span>
            </div>

            <!-- Séparateur visuel élégant -->
            <div class="player-divider d-none d-sm-block"></div>

            <!-- Contrôleur de Positionnement du Lecteur (Dock Left / Center / Right) -->
            <div class="player-dock-selector d-inline-flex align-items-center p-1 rounded-pill" title="Positionner la barre flottante sur l'écran">
                <button type="button" class="btn-player-dock" data-dock-action="left" title="Ancrer à gauche" aria-label="Ancrer à gauche">
                    <span class="material-symbols-rounded">format_align_left</span>
                </button>
                <button type="button" class="btn-player-dock active" data-dock-action="center" title="Centrer le lecteur" aria-label="Centrer">
                    <span class="material-symbols-rounded">format_align_center</span>
                </button>
                <button type="button" class="btn-player-dock" data-dock-action="right" title="Ancrer à droite" aria-label="Ancrer à droite">
                    <span class="material-symbols-rounded">format_align_right</span>
                </button>
            </div>
        </div>

    </div>
</div>


<!-- ═════════════════════════════════════════════════════════════════════════ -->
<!-- B. MINI-LECTEUR MOBILE (Affiché en bas d'écran sur mobile < 768px)         -->
<!-- ═════════════════════════════════════════════════════════════════════════ -->
<div id="mobileMiniPlayer" class="mobile-mini-player d-md-none d-none" role="button" aria-label="Ouvrir le lecteur complet">
    <!-- Fine barre de progression au sommet du mini-lecteur -->
    <div class="mini-player-progress-bar">
        <div id="miniPlayerProgressFill" class="mini-player-progress-fill"></div>
    </div>

    <div class="mini-player-body d-flex align-items-center justify-content-between px-3 py-2">
        <!-- Pochette 1:1 + Métadonnées -->
        <div class="d-flex align-items-center gap-3 overflow-hidden flex-grow-1" id="miniPlayerOpenTrigger">
            <div class="mini-player-cover-box position-relative flex-shrink-0">
                <img id="miniPlayerCover" src="assets/images/micro-ensad-icon.svg" alt="Pochette de l'épisode" class="mini-player-thumb">
                <span class="player-pulse-indicator" style="top: 2px; right: 2px; width: 8px; height: 8px;"></span>
            </div>
            <div class="overflow-hidden flex-grow-1">
                <h6 id="miniPlayerTitle" class="mb-0 text-truncate font-fredoka fw-bold small text-white">The Next Chapter</h6>
                <div class="d-flex align-items-center gap-2">
                    <p id="miniPlayerArtist" class="mb-0 text-muted small text-truncate" style="font-size: 0.75rem;">Oussama Laabidate</p>
                    <span id="miniPlayerChapterBadge" class="badge-mini-chapter text-truncate d-none"></span>
                </div>
            </div>
        </div>

        <!-- Bouton Play/Pause mobile & Fermer -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
            <button type="button" class="mini-player-play-btn" id="miniPlayerPlayBtn" aria-label="Lecture ou pause">
                <span class="material-symbols-rounded" id="miniPlayerPlayIcon">play_arrow</span>
            </button>
            <button type="button" class="mini-player-btn-action" id="miniPlayerExpandBtn" aria-label="Agrandir en plein écran">
                <span class="material-symbols-rounded">expand_less</span>
            </button>
        </div>
    </div>
</div>


<!-- ═════════════════════════════════════════════════════════════════════════ -->
<!-- C. LECTEUR MOBILE PLEIN ÉCRAN STYLE SPOTIFY (Inspiré fidèlement)          -->
<!-- ═════════════════════════════════════════════════════════════════════════ -->
<div id="spotifyMobilePlayer" class="spotify-mobile-player d-md-none" aria-hidden="true">
    <!-- Lueur ambiante dynamique d'arrière-plan -->
    <div class="spotify-ambient-glow" id="spotifyAmbientGlow"></div>

    <div class="spotify-player-container d-flex flex-column justify-content-between h-100">
        
        <!-- 1. En-tête supérieur Spotify (Minimize + Titre émission + Partage) -->
        <div class="spotify-header d-flex align-items-center justify-content-between px-4 pt-3 pb-2">
            <button type="button" class="btn-spotify-icon" id="spotifyCloseBtn" aria-label="Réduire le lecteur">
                <span class="material-symbols-rounded" style="font-size: 1.8rem;">keyboard_arrow_down</span>
            </button>
            
            <div class="text-center overflow-hidden px-2">
                <p class="spotify-header-label mb-0 text-uppercase tracking-wider text-muted font-monospace" style="font-size: 0.7rem; letter-spacing: 0.12em;">Lecture du podcast</p>
                <h6 class="spotify-header-podcast mb-0 text-truncate fw-bold text-white small" id="spotifyPodcastName">Micro ENSAD</h6>
            </div>

            <button type="button" class="btn-spotify-icon" id="spotifyHeaderShareBtn" aria-label="Partager avec QR Code">
                <span class="material-symbols-rounded" style="font-size: 1.4rem;">share</span>
            </button>
        </div>

        <!-- 2. Grande Pochette 1:1 Carrée avec Coins Arrondis (Signature Spotify) -->
        <div class="spotify-artwork-stage d-flex align-items-center justify-content-center px-4 my-auto">
            <div class="spotify-cover-box position-relative">
                <img id="spotifyCover" src="assets/images/micro-ensad-icon.svg" alt="Pochette officielle" class="spotify-cover-img">
            </div>
        </div>

        <!-- 3. Section Métadonnées & Like / Signet -->
        <div class="spotify-meta-section px-4 pt-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="overflow-hidden pe-3">
                    <h4 id="spotifyTitle" class="spotify-track-title mb-1 text-white fw-bold text-truncate">The Next Chapter</h4>
                    <p id="spotifyArtist" class="spotify-track-host mb-0 text-muted text-truncate small">Oussama Laabidate • ENSAD Casablanca</p>
                </div>
                <button type="button" class="btn-spotify-icon flex-shrink-0" id="spotifyLikeBtn" aria-label="Ajouter aux favoris">
                    <span class="material-symbols-rounded" id="spotifyLikeIcon" style="font-size: 1.6rem;">favorite_border</span>
                </button>
            </div>

            <!-- Pastille Chapitre Cliquable -->
            <div class="spotify-chapter-pill-row d-flex align-items-center justify-content-center mt-2 mb-3">
                <button type="button" class="spotify-chapter-pill d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" id="spotifyChapterPillBtn">
                    <span class="material-symbols-rounded" style="font-size: 0.9rem; color: var(--electric-blue);">bookmark</span>
                    <span id="spotifyChapterTitle" class="text-truncate small fw-medium" style="max-width: 260px;">Partie 1 • Introduction</span>
                    <span class="material-symbols-rounded text-muted" style="font-size: 0.9rem;">chevron_right</span>
                </button>
            </div>
        </div>

        <!-- 4. Scrubber & Timeline Progress Bar (YouTube Segments sur Mobile) -->
        <div class="spotify-timeline-section px-4">
            <div class="spotify-timeline-interactive-wrap position-relative" id="spotifyTimelineWrap">
                <!-- Segments de chapitres YouTube en arrière-plan -->
                <div class="player-chapters-track" id="spotifyChaptersTrack"></div>

                <!-- Input range pour le contrôle tactile fluide -->
                <input type="range" class="slider-timeline slider-timeline-youtube w-100" id="spotifyProgressBar" min="0" max="100" value="0" step="0.1" aria-label="Progression de lecture">
            </div>

            <div class="d-flex align-items-center justify-content-between text-muted small font-monospace mt-1" style="font-size: 0.75rem;">
                <span id="spotifyCurrentTime">00:00</span>
                <span id="spotifyDuration">00:00</span>
            </div>
        </div>

        <!-- 5. Commandes Principales Spotify (Saut 15s, Prev, Gros Bouton Blanc, Next, Saut 15s) -->
        <div class="spotify-controls-section d-flex align-items-center justify-content-between px-4 py-3">
            <!-- Saut -15s -->
            <button type="button" class="btn-spotify-action" id="spotifyBack15" aria-label="Reculer de 15 secondes">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="player-svg-icon" aria-hidden="true">
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L2 9"></path>
                    <polyline points="2 4 2 9 7 9"></polyline>
                    <text x="12" y="15.5" font-size="7.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none" font-family="'Montserrat', sans-serif">15</text>
                </svg>
            </button>

            <!-- Chapitre Précédent -->
            <button type="button" class="btn-spotify-action" id="spotifyPrevChBtn" aria-label="Chapitre précédent">
                <span class="material-symbols-rounded" style="font-size: 2rem;">skip_previous</span>
            </button>

            <!-- ⭐️ GROS BOUTON BLANC CIRCULAIRE CENTRAL SPOTIFY (64px) ⭐️ -->
            <button type="button" class="spotify-play-btn-circle" id="spotifyPlayBtn" aria-label="Lecture ou pause">
                <span class="material-symbols-rounded" id="spotifyPlayIcon">play_arrow</span>
            </button>

            <!-- Chapitre Suivant -->
            <button type="button" class="btn-spotify-action" id="spotifyNextChBtn" aria-label="Chapitre suivant">
                <span class="material-symbols-rounded" style="font-size: 2rem;">skip_next</span>
            </button>

            <!-- Saut +15s -->
            <button type="button" class="btn-spotify-action" id="spotifyFwd15" aria-label="Avancer de 15 secondes">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="player-svg-icon" aria-hidden="true">
                    <path d="M20.49 15a9 9 0 1 1-2.13-9.36L22 9"></path>
                    <polyline points="22 4 22 9 17 9"></polyline>
                    <text x="12" y="15.5" font-size="7.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none" font-family="'Montserrat', sans-serif">15</text>
                </svg>
            </button>
        </div>

        <!-- 6. Barre d'Outils Inférieure (Vitesse, Chapitres, Partage) -->
        <div class="spotify-bottom-tools d-flex align-items-center justify-content-between px-4 pb-4 pt-2">
            <!-- Vitesse de lecture (1x, 1.25x, 1.5x) -->
            <button type="button" class="btn-spotify-tool font-monospace fw-bold" id="spotifySpeedBtn" title="Vitesse de lecture">
                <span id="spotifySpeedText">1x</span>
            </button>

            <!-- Liste des chapitres -->
            <button type="button" class="btn-spotify-tool" id="spotifyChaptersListBtn" title="Afficher tous les chapitres">
                <span class="material-symbols-rounded" style="font-size: 1.35rem;">format_list_bulleted</span>
            </button>

            <!-- Partage avec QR Code -->
            <button type="button" class="btn-spotify-tool" id="spotifyShareActionBtn" title="Partager le podcast">
                <span class="material-symbols-rounded" style="font-size: 1.35rem;">qr_code_2</span>
            </button>
        </div>

    </div>
</div>


<!-- ═════════════════════════════════════════════════════════════════════════ -->
<!-- D. TIROIR DES CHAPITRES (Bottom Sheet / Modal interactif)                 -->
<!-- ═════════════════════════════════════════════════════════════════════════ -->
<div id="playerChaptersDrawer" class="player-chapters-drawer d-none" aria-hidden="true">
    <div class="chapters-drawer-overlay" id="chaptersDrawerOverlay"></div>
    <div class="chapters-drawer-content">
        <!-- Barre de poignée tactile pour glisser vers le bas -->
        <div class="drawer-drag-handle-bar my-2 mx-auto"></div>

        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-primary">bookmark</span>
                <h5 class="mb-0 fw-bold text-white font-fredoka">Chapitres du Podcast</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" id="chaptersDrawerCloseBtn" aria-label="Fermer"></button>
        </div>

        <!-- Liste dynamique des 4 chapitres cliquables -->
        <div class="chapters-drawer-list p-3" id="chaptersDrawerList">
            <!-- Rempli dynamiquement en JS -->
        </div>
    </div>
</div>
