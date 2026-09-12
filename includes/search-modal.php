<?php
/**
 * Modale de Recherche Instantanée - Micro ENSAD
 * Interface sombre cinématique pour explorer rapidement les 11 épisodes des étudiants.
 */
?>
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-labelledby="globalSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content search-modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary" style="font-size: 1.5rem;">search</span>
                    <h5 class="modal-title font-heading fw-bold text-white mb-0" id="globalSearchModalLabel">Rechercher un Épisode</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="search-input-wrapper position-relative mb-4">
                    <input type="text" id="liveSearchModalInput" class="form-control search-input-field" placeholder="Titre, invité, filière, sujet (ex: Shoot, IA, Design, Manga...)" autocomplete="off">
                    <span class="material-symbols-rounded search-input-icon">mic</span>
                </div>

                <div class="search-quick-tags mb-3">
                    <span class="small text-muted me-2">Suggestions :</span>
                    <button type="button" class="badge-search-pill" data-quick-search="IA">IA &amp; Création</button>
                    <button type="button" class="badge-search-pill" data-quick-search="Shoot">Shoot</button>
                    <button type="button" class="badge-search-pill" data-quick-search="Gaming">Jeux Vidéo</button>
                    <button type="button" class="badge-search-pill" data-quick-search="Design">Design Graphique</button>
                    <button type="button" class="badge-search-pill" data-quick-search="Football">Sport &amp; Art</button>
                </div>

                <!-- Résultats en direct -->
                <div id="searchModalResults" class="search-results-list">
                    <div class="text-center py-4 text-muted small">
                        Tapez quelques lettres pour rechercher parmi les 11 épisodes officiels de l'ENSAD Mohammedia.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
