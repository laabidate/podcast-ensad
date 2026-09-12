<?php
/**
 * Modale de Partage Cinématique avec QR Code & Carte Story (9:16)
 * Micro ENSAD Mohammedia — 100% Statique (Zéro DB)
 * Permet de visualiser, scanner le QR Code et télécharger la Carte Story en PNG HD (1080x1920).
 */
?>

<!-- ===== MODALE DE PARTAGE AVEC CARTE QR CODE ===== -->
<div class="modal fade" id="qrShareModal" tabindex="-1" aria-labelledby="qrShareModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-2xl overflow-hidden qr-modal-content">

            <!-- En-tête de la modale -->
            <div class="modal-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="qr-modal-icon-badge">
                        <span class="material-symbols-rounded">qr_code_2</span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="qrShareModalTitle" style="font-family:'Plus Jakarta Sans',sans-serif; letter-spacing:-0.3px;">
                            Carte de Partage & QR Code
                        </h5>
                        <p class="text-muted small mb-0">Partagez l'épisode sur Instagram, WhatsApp, Facebook et vos réseaux</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4 pt-2">
                <div class="row g-4 align-items-center">

                    <!-- COLONNE GAUCHE : PRÉVISUALISATION CARTE STORY 9:16 -->
                    <div class="col-12 col-lg-5 col-xl-5 d-flex justify-content-center">
                        <div class="qr-story-preview-frame">
                            <!-- Carte Story Visuelle (Format 9:16) -->
                            <div class="qr-story-card" id="storyCardVisual">
                                <!-- Fond flouté dynamique de l'affiche de l'épisode -->
                                <img id="storyCardBgImg" src="" alt="Fond de la carte" class="qr-story-bg-layer">
                                <div class="qr-story-overlay-layer"></div>

                                <!-- Contenu interne de la carte -->
                                <div class="qr-story-inner">
                                    <!-- 1. En-tête avec les Deux Logos Officiels -->
                                    <div class="qr-story-logos-header d-flex align-items-center justify-content-between w-100 mb-2">
                                        <!-- Logo Principal : ENSAD Casablanca -->
                                        <div class="qr-story-logo-wrap qr-logo-principal-box">
                                            <img src="assets/images/logo-principal.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-principal.svg') ?>" alt="Logo ENSAD" class="qr-story-logo-img qr-logo-ensad">
                                        </div>

                                        <!-- Logo Secondaire : Université Hassan II Casablanca -->
                                        <div class="qr-story-logo-wrap qr-logo-secondaire-box">
                                            <img src="assets/images/logo-secondaire.svg?v=<?= filemtime(__DIR__ . '/../assets/images/logo-secondaire.svg') ?>" alt="Logo Université Hassan II" class="qr-story-logo-img qr-logo-uh2c">
                                        </div>
                                    </div>

                                    <!-- Badge Catégorie sous les logos -->
                                    <div class="w-100 text-center mb-2.5">
                                        <span id="storyCardCategory" class="badge rounded-pill qr-category-pill"></span>
                                    </div>

                                    <!-- 2. Affiche / Pochette carrée 1:1 de l'épisode -->
                                    <div class="qr-story-cover-wrap mb-2.5 position-relative">
                                        <img id="storyCardCoverImg" src="" alt="Pochette de l'épisode" class="qr-story-cover-img">
                                        <span id="storyCardEpBadge" class="qr-story-ep-tag">EP. 01</span>
                                    </div>

                                    <!-- 3. Titre et Métadonnées -->
                                    <div class="text-center w-100 mb-3">
                                        <div id="storyCardShowTitle" class="qr-story-show-name mb-1">Émission</div>
                                        <h4 id="storyCardTitle" class="qr-story-title mb-2">Titre de l'épisode</h4>
                                        <div id="storyCardHosts" class="qr-story-hosts text-truncate">Étudiants</div>
                                    </div>

                                    <!-- 4. Section QR Code interactif -->
                                    <div class="qr-code-box-wrapper mb-2">
                                        <div class="qr-code-white-box" id="storyQrCodeMount">
                                            <!-- QR Code injecté ici dynamiquement par qrcode.js -->
                                        </div>
                                    </div>

                                    <!-- 5. Footer institutionnel -->
                                    <div class="qr-story-footer mt-auto pt-2 text-center w-100">
                                        <div class="small fw-semibold" style="font-size:0.65rem; color:rgba(255,255,255,0.75);">
                                            ENSAD Casablanca • Université Hassan II
                                        </div>
                                        <div id="storyCardDept" style="font-size:0.58rem; color:rgba(255,255,255,0.45);">
                                            Cycle Bac+5 (DENSAD)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLONNE DROITE : ACTIONS DE TÉLÉCHARGEMENT & PARTAGE SOCIAL -->
                    <div class="col-12 col-lg-7 col-xl-7">
                        <div class="qr-actions-panel p-3 p-sm-4 rounded-4">

                            <!-- Bloc 1 : Action Principale (Télécharger l'Image Story HD) -->
                            <div class="mb-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2.5 py-1 mb-2" style="font-size:0.68rem;letter-spacing:0.5px;">
                                    <span class="material-symbols-rounded align-middle me-1" style="font-size:0.85rem;">auto_awesome</span> Format Story 9:16
                                </span>
                                <h5 class="fw-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-main);">
                                    Télécharger la Carte Visuelle
                                </h5>
                                <p class="text-muted small mb-3">
                                    Générez instantanément une image haute définition (1080×1920 pixels) de cet épisode avec son QR Code, prête à être partagée en Story Instagram, Facebook ou statut WhatsApp.
                                </p>
                                <button type="button" class="btn btn-ensad-yellow w-100 py-3 d-flex align-items-center justify-content-center gap-2 shadow-md hover-scale" id="btnDownloadStoryPng">
                                    <span class="material-symbols-rounded fs-5">download</span>
                                    <span class="fw-bold">Télécharger l'Image Story (PNG HD)</span>
                                </button>
                                <div id="storyGenProgress" class="d-none mt-2 text-center small text-primary fw-semibold">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Génération haute résolution en cours...
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: var(--border-color);">

                            <!-- Bloc 2 : Partage en 1 Clic sur les Réseaux -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.5px;">
                                    Partager directement sur vos réseaux :
                                </label>
                                <div class="row g-2">
                                    <!-- WhatsApp -->
                                    <div class="col-6 col-sm-3">
                                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-social-share btn-whatsapp w-100" id="shareBtnWhatsapp" title="Partager sur WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                    </div>
                                    <!-- Facebook -->
                                    <div class="col-6 col-sm-3">
                                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-social-share btn-facebook w-100" id="shareBtnFacebook" title="Partager sur Facebook">
                                            <i class="bi bi-facebook"></i>
                                            <span>Facebook</span>
                                        </a>
                                    </div>
                                    <!-- LinkedIn -->
                                    <div class="col-6 col-sm-3">
                                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-social-share btn-linkedin w-100" id="shareBtnLinkedin" title="Partager sur LinkedIn">
                                            <i class="bi bi-linkedin"></i>
                                            <span>LinkedIn</span>
                                        </a>
                                    </div>
                                    <!-- X / Twitter -->
                                    <div class="col-6 col-sm-3">
                                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-social-share btn-twitter w-100" id="shareBtnTwitter" title="Partager sur X">
                                            <i class="bi bi-twitter-x"></i>
                                            <span>X (Twitter)</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Bloc 3 : Partage Mobile Natif ou Copie de Lien -->
                            <div>
                                <label class="form-label small fw-bold text-uppercase text-muted mb-2" style="font-size:0.72rem;letter-spacing:0.5px;">
                                    Lien direct de l'épisode :
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-surface border" style="color:var(--text-muted);">
                                        <span class="material-symbols-rounded" style="font-size:1.1rem;">link</span>
                                    </span>
                                    <input type="text" class="form-control font-monospace text-truncate" id="qrShareDirectUrl" readonly style="font-size:0.85rem;background:var(--bg-surface-subtle);color:var(--text-main);">
                                    <button class="btn btn-primary d-inline-flex align-items-center gap-1 px-3" type="button" id="btnCopyShareLink">
                                        <span class="material-symbols-rounded" style="font-size:1.05rem;" id="copyBtnIcon">content_copy</span>
                                        <span id="copyBtnText">Copier</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Bouton Mobile Natif Web Share (Visible sur téléphones compatibles) -->
                            <div class="mt-3 d-none" id="nativeShareWrap">
                                <button type="button" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-pill" id="btnNativeWebShare">
                                    <span class="material-symbols-rounded">share</span>
                                    <span>Partager via l'application de votre téléphone</span>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer modale -->
            <div class="modal-footer border-0 px-4 py-3 justify-content-between" style="background:var(--bg-surface-subtle);">
                <small class="text-muted d-flex align-items-center gap-1">
                    <span class="material-symbols-rounded text-primary" style="font-size:1rem;">verified</span>
                    <span>Micro ENSAD • Plateforme Officielle de Podcasts Étudiants</span>
                </small>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Fermer
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Canvas invisible servant à la génération haute résolution PNG (1080x1920) -->
<canvas id="storyExportCanvas" width="1080" height="1920" style="display:none;"></canvas>
