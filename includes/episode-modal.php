<?php
/**
 * Modal de Détails d'Épisode - Micro ENSAD Mohammedia
 * Bootstrap 5 — Remplie dynamiquement via JavaScript depuis data-episode-json
 * Compatible Dark Mode & Icônes Google Material Symbols.
 */
?>

<!-- ===== MODALE DÉTAILS ÉPISODE ===== -->
<div class="modal fade" id="episodeDetailModal" tabindex="-1" aria-labelledby="episodeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header Héro avec Fond Flouté Ambiant + Image Carrée 1:1 Complète + Métadonnées -->
            <div class="modal-episode-header position-relative overflow-hidden p-3 p-sm-4">
                <!-- Arrière-plan flou atmosphérique (couleurs de la pochette) -->
                <img id="modalCoverBg" src="" alt="" class="modal-hero-bg-blur" aria-hidden="true">
                <div class="modal-hero-overlay"></div>

                <!-- Bouton fermer positionné en haut à droite -->
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-sm z-3" data-bs-dismiss="modal" aria-label="Fermer"></button>

                <!-- Conteneur Principal : Image Carrée (1:1) + Titre & Badges -->
                <div class="position-relative z-2 d-flex flex-column flex-sm-row align-items-center align-items-sm-center gap-3 gap-sm-4 pt-1">
                    
                    <!-- Pochette Carrée 1:1 Complète (SQUARE ARTWORK) -->
                    <div class="modal-square-cover-box flex-shrink-0">
                        <img id="modalCoverImg" src="" alt="" class="modal-square-cover-img">
                        <span id="modalEpTagCorner" class="modal-square-corner-badge"></span>
                    </div>

                    <!-- Informations & Titre à droite de l'image -->
                    <div class="flex-grow-1 text-center text-sm-start min-w-0 pt-1">
                        <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mb-2 flex-wrap">
                            <span id="modalEpTag" class="badge rounded-pill fw-bold" style="background: var(--ensad-yellow); color: #092A5C; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.76rem;"></span>
                            <span id="modalCategoryTag" class="badge rounded-pill text-white border border-white border-opacity-30" style="font-size: 0.72rem; background: rgba(255,255,255,0.18); backdrop-filter: blur(8px);"></span>
                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: rgba(18,100,255,0.25); border: 1px solid rgba(47,123,255,0.5); color: #60A5FA; font-size: 0.72rem;">
                                <span class="material-symbols-rounded" style="font-size: 0.85rem;">graphic_eq</span> YouTube Audio
                            </span>
                        </div>

                        <h4 id="episodeModalTitle" class="mb-2 text-white fw-bold lh-sm text-break" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.35rem; text-shadow: 0 2px 10px rgba(0,0,0,0.6);"></h4>

                        <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 text-white-50 small flex-wrap">
                            <span class="d-inline-flex align-items-center gap-1 text-white">
                                <span class="material-symbols-rounded text-primary" style="font-size: 1.05rem;">podcasts</span>
                                <strong id="modalHeaderPodcastTitle"></strong>
                            </span>
                            <span>•</span>
                            <span id="modalHeaderDuration" class="font-monospace text-white-50"></span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Corps -->
            <div class="modal-body p-0">
                <div class="px-4 py-3">

                    <!-- Ligne : Podcast + Date + Durée -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3 pb-3 border-bottom" style="border-color: var(--border-color)!important;">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="material-symbols-rounded text-primary" style="font-size: 1.05rem;">podcasts</span>
                            <span id="modalPodcastTitle" class="fw-semibold" style="color: var(--text-main);"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1 text-muted small">
                            <span class="material-symbols-rounded text-muted" style="font-size: 1.05rem;">calendar_today</span>
                            <span id="modalDate"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1 text-muted small">
                            <span class="material-symbols-rounded text-primary" style="font-size: 1.05rem;">schedule</span>
                            <span id="modalDuration" class="font-monospace fw-semibold"></span>
                        </div>
                    </div>

                    <!-- Contributeurs étudiants -->
                    <div id="modalContributorsWrap" class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-rounded text-primary" style="font-size: 1.1rem;">group</span>
                            <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Étudiants</span>
                        </div>
                        <div id="modalContributorBadges" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <!-- Description -->
                    <div id="modalDescWrap" class="mb-3 pb-3 border-bottom" style="border-color: var(--border-color)!important;">
                        <p id="modalDescription" class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.7;"></p>
                    </div>

                    <!-- Encadrement académique -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-light);">
                        <div class="flex-shrink-0" style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--ensad-blue), var(--ensad-navy)); display: flex; align-items: center; justify-content: center;">
                            <span class="material-symbols-rounded text-white" style="font-size: 1.2rem;">school</span>
                        </div>
                        <div>
                            <div class="small fw-bold" style="color: var(--text-main); font-size: 0.8rem;">Encadrement Académique</div>
                            <div class="small text-muted" style="font-size: 0.78rem;">Mme Randa El Amraoui — Module Français, DENSAD Bac+5 (S2)</div>
                            <div id="modalFiliere" class="small text-muted" style="font-size: 0.75rem;"></div>
                        </div>
                    </div>

                    <!-- Chapitres -->
                    <div id="modalChaptersWrap" class="d-none mb-2">
                        <div class="small fw-bold text-uppercase text-muted mb-2 d-flex align-items-center gap-1" style="letter-spacing: 0.5px; font-size: 0.68rem;">
                            <span class="material-symbols-rounded text-primary" style="font-size: 1rem;">format_list_bulleted</span>
                            <span>Chapitres</span>
                        </div>
                        <div id="modalChaptersList" class="d-flex flex-column gap-1"></div>
                    </div>

                </div>
            </div>

            <!-- Footer avec boutons d'action -->
            <div class="modal-footer border-0 px-4 py-3 gap-2" style="background: var(--bg-surface-subtle); border-top: 1px solid var(--border-color)!important;">
                <button type="button" class="btn btn-ensad-yellow flex-grow-1" id="modalPlayBtn">
                    <span class="material-symbols-rounded fs-5">play_circle</span>
                    <span>Écouter l'Audio</span>
                </button>
                <button type="button" class="btn btn-ensad-outline d-inline-flex align-items-center gap-1" id="modalShareQrBtn" title="Générer Carte QR Code & Story">
                    <span class="material-symbols-rounded text-primary" style="font-size: 1.1rem;">qr_code_2</span>
                    <span>Partager / QR</span>
                </button>
                <a href="#" class="btn btn-ensad-outline" id="modalDetailLink" target="_self">
                    <span class="material-symbols-rounded" style="font-size: 1.1rem;">info</span>
                    <span>Fiche</span>
                </a>
            </div>

        </div>
    </div>
</div>
