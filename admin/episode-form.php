<?php
/**
 * Episode Add / Edit Form — Micro ENSAD Admin
 * Si ?id=X → mode édition (pré-rempli)
 * Sinon   → mode ajout (vide)
 */
require_once __DIR__ . '/auth.php';

$data     = adminLoadData();
$episodes = $data['episodes'] ?? [];
$podcasts = $data['podcasts'] ?? [];

$isEdit  = false;
$ep      = [];
$epId    = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($epId) {
    foreach ($episodes as $e) {
        if ((int)$e['id'] === $epId) { $ep = $e; $isEdit = true; break; }
    }
    if (!$isEdit) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => "Épisode #{$epId} introuvable."];
        header('Location: index.php');
        exit;
    }
}

$pageTitle = $isEdit ? "Modifier : " . ($ep['title'] ?? '') : "Ajouter un épisode";
$activeNav = 'add';

// Catégories disponibles (depuis les podcasts existants)
$cats = array_unique(array_filter(array_column($podcasts, 'category')));
$selectedPodcastId = (int)($ep['podcast_id'] ?? ($podcasts[0]['id'] ?? 0));

require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php" class="btn-icon" style="color:#64748B;" title="Retour">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
            <?= $isEdit ? '<i class="bi bi-pencil-square me-2 text-primary"></i>Modifier l\'épisode' : '<i class="bi bi-plus-circle me-2 text-primary"></i>Nouvel épisode' ?>
        </h5>
        <span class="small text-muted">
            <?= $isEdit ? 'Les modifications seront écrites directement dans episodes.json' : 'Le nouvel épisode sera ajouté à episodes.json' ?>
        </span>
    </div>
</div>

<div class="admin-alert info">
    <i class="bi bi-file-earmark-code-fill fs-5"></i>
    <span>
        <strong>Fichier cible :</strong> <code>data/episodes.json</code> — les changements sont immédiats, pas de base de données.
    </span>
</div>

<form method="POST" action="save.php" enctype="multipart/form-data" id="episodeForm">
    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
    <input type="hidden" name="mode"       value="<?= $isEdit ? 'edit' : 'add' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="episode_id" value="<?= (int)$ep['id'] ?>">
    <?php endif; ?>

    <div class="row g-4">

        <!-- ── COLONNE GAUCHE ─────────────────────── -->
        <div class="col-12 col-xl-8">

            <!-- 1. Infos générales -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-info-circle-fill text-primary"></i> Informations générales
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Titre de l'épisode <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc($ep['title'] ?? '') ?>"
                               placeholder="Ex : L'IA outil créatif ou menace ?" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Numéro d'épisode</label>
                        <input type="text" name="ep_number" class="form-control"
                               value="<?= esc($ep['ep_number'] ?? '') ?>"
                               placeholder="Ex : EP. 12">
                        <div class="form-hint">Laissez vide pour auto-incrémenter.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Date / Semestre</label>
                        <input type="text" name="date" class="form-control"
                               value="<?= esc($ep['date'] ?? 'Semestre 2 — 2025/2026') ?>"
                               placeholder="Ex : Semestre 2 — 2025/2026">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Résumé de l'épisode..." required><?= esc($ep['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Émission / Podcast -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-broadcast text-primary"></i> Émission & Catégorie
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Emission / Podcast <span class="text-danger">*</span></label>
                        <select name="podcast_id" class="form-select" required>
                            <option value="">-- Selectionner une emission --</option>
                            <?php foreach ($podcasts as $podcast): ?>
                                <option value="<?= (int)$podcast['id'] ?>" <?= $selectedPodcastId === (int)$podcast['id'] ? 'selected' : '' ?>>
                                    <?= esc($podcast['title'] ?? '') ?> - <?= esc($podcast['category'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint">
                            Les emissions se gerent depuis la page <a href="podcast-form.php">Ajouter une emission</a>.
                        </div>
                    </div>
                    <div class="col-sm-6 d-none">
                        <label class="form-label">Nom de l'emission</label>
                        <input type="text" name="podcast_title" class="form-control"
                               value="<?= esc($ep['podcast_title'] ?? '') ?>"
                               placeholder="Ex : Art.exe">
                    </div>
                    <div class="col-sm-6 d-none">
                        <label class="form-label">Catégorie / Thématique</label>
                        <input type="text" name="podcast_category" class="form-control"
                               list="catList"
                               value="<?= esc($ep['podcast_category'] ?? '') ?>"
                               placeholder="Ex : IA & Création">
                        <datalist id="catList">
                            <?php foreach ($cats as $cat): ?>
                                <option value="<?= esc($cat) ?>">
                            <?php endforeach; ?>
                        </datalist>
                        <div class="form-hint">Sélectionnez ou tapez une nouvelle catégorie.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Filière</label>
                        <select name="filiere" class="form-select">
                            <option value="">— Sélectionner —</option>
                            <option value="Design Graphique & Interactif (DGI)" <?= ($ep['filiere'] ?? '') === 'Design Graphique & Interactif (DGI)' ? 'selected' : '' ?>>DGI — Design Graphique & Interactif</option>
                            <option value="Game Design & Animation (GDA)" <?= ($ep['filiere'] ?? '') === 'Game Design & Animation (GDA)' ? 'selected' : '' ?>>GDA — Game Design & Animation</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Animateur principal (Host)</label>
                        <input type="text" name="host" class="form-control"
                               value="<?= esc($ep['host'] ?? '') ?>"
                               placeholder="Nom de l'animateur principal">
                    </div>
                </div>
            </div>

            <!-- 3. Audio YouTube -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-youtube text-danger"></i> Audio YouTube
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Lien YouTube ou ID de la vidéo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:0.82rem;background:#F8FAFC;">
                                <i class="bi bi-youtube text-danger me-1"></i> youtube.com/watch?v=
                            </span>
                            <input type="text" name="youtube_input" class="form-control"
                                   value="<?= esc($ep['youtube_id'] ?? $ep['youtube_url'] ?? '') ?>"
                                   placeholder="ID ou URL YouTube complète" required>
                        </div>
                        <div class="form-hint">
                            Collez l'URL complète (<code>https://youtube.com/watch?v=XXXX</code>) ou juste l'ID (<code>XXXX</code>).
                            L'ID sera extrait automatiquement.
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Durée (MM:SS)</label>
                        <input type="text" name="duration" class="form-control font-monospace"
                               value="<?= esc($ep['duration'] ?? '') ?>"
                               placeholder="Ex : 18:40"
                               pattern="[0-9]{1,2}:[0-9]{2}">
                        <div class="form-hint">Format MM:SS ou HH:MM:SS</div>
                    </div>
                </div>
            </div>

            <!-- 4. Contributeurs étudiants -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-people-fill text-primary"></i> Étudiants contributeurs
                </div>
                <div class="form-hint mb-3">Ajoutez les noms des étudiants qui ont participé à cet épisode.</div>
                <div id="contribList">
                    <?php
                    $contributors = $ep['contributors'] ?? [''];
                    if (empty($contributors)) $contributors = [''];
                    foreach ($contributors as $i => $c):
                    ?>
                    <div class="contrib-row">
                        <input type="text" name="contributors[]" class="form-control"
                               value="<?= esc($c) ?>"
                               placeholder="Prénom Nom de l'étudiant(e)">
                        <button type="button" class="btn-icon del" onclick="removeRow(this)" title="Retirer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mt-2 px-3"
                        onclick="addContrib()">
                    <i class="bi bi-plus me-1"></i> Ajouter un étudiant
                </button>
            </div>

            <!-- 5. Chapitres -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-list-ol text-primary"></i> Chapitres (optionnel)
                </div>
                <div class="form-hint mb-3">Chaque chapitre aura un bouton cliquable dans la modale.</div>
                <div class="row g-1 mb-2">
                    <div class="col" style="max-width:88px;"><small class="text-muted fw-semibold">Temps (MM:SS)</small></div>
                    <div class="col"><small class="text-muted fw-semibold">Titre du chapitre</small></div>
                    <div style="width:44px;"></div>
                </div>
                <div id="chapterList">
                    <?php
                    $chapters = $ep['chapters'] ?? [];
                    if (empty($chapters)) $chapters = [['time' => '', 'title' => '']];
                    foreach ($chapters as $ch):
                    ?>
                    <div class="chapter-row">
                        <input type="text" name="chapter_time[]" class="form-control font-monospace"
                               value="<?= esc($ch['time'] ?? '') ?>" placeholder="00:00">
                        <input type="text" name="chapter_title[]" class="form-control"
                               value="<?= esc($ch['title'] ?? '') ?>" placeholder="Titre du chapitre...">
                        <button type="button" class="btn-icon del" onclick="removeRow(this)" title="Retirer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mt-2 px-3"
                        onclick="addChapter()">
                    <i class="bi bi-plus me-1"></i> Ajouter un chapitre
                </button>
            </div>

        </div><!-- /col gauche -->

        <!-- ── COLONNE DROITE ─────────────────────── -->
        <div class="col-12 col-xl-4">

            <!-- Image de couverture : Zone dédiée d'Upload avec Prévisualisation en Direct -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-image text-primary"></i> Image de couverture</span>
                    <span class="badge bg-light text-primary border" style="font-size:0.7rem;">Zone Dédiée</span>
                </div>

                <!-- Aperçu en Direct Haute Définition (16:10) -->
                <div class="admin-preview-box mb-3" id="coverPreviewBox">
                    <img id="coverPreviewImg" src="<?= !empty($ep['cover']) ? ('../' . esc($ep['cover'])) : '../assets/images/micro-ensad-icon.svg' ?>" 
                         alt="Aperçu couverture" class="admin-preview-img">
                    <div class="position-absolute bottom-0 start-0 p-2 w-100 d-flex align-items-center justify-content-between" style="background:linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);">
                        <span class="badge bg-primary font-monospace" style="font-size:0.65rem;" id="coverPreviewBadge">
                            <?= !empty($ep['cover']) ? 'Image Actuelle' : 'Image par défaut' ?>
                        </span>
                        <span class="small text-white-50 font-monospace text-truncate ms-2" style="font-size:0.7rem; max-width:180px;" id="coverFileInfo">
                            <?= !empty($ep['cover']) ? esc(basename($ep['cover'])) : '' ?>
                        </span>
                    </div>
                </div>

                <!-- Zone Dédiée de Téléversement (Dropzone Visuelle) -->
                <div class="admin-dropzone-box mb-3" id="adminDropzone">
                    <div class="admin-dropzone-icon">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color:#092A5C; font-size:0.95rem;">
                        Glissez-déposez votre image ici
                    </h6>
                    <p class="text-muted small mb-2" style="font-size:0.78rem;">
                        ou cliquez pour choisir un fichier depuis votre appareil
                    </p>
                    <span class="badge bg-white text-secondary border px-3 py-1 rounded-pill small">
                        JPG, PNG, WebP ou GIF (max 10 Mo)
                    </span>
                    <input type="file" name="cover_upload" id="coverFileInput" class="d-none"
                           accept="image/jpeg,image/png,image/gif,image/webp">
                </div>

                <!-- Option alternative : Sélectionner dans la galerie existante -->
                <div class="pt-2 border-top" style="border-color:#EDF4FD;">
                    <label class="form-label small text-muted mb-1">
                        <i class="bi bi-images me-1"></i> Ou choisir une image de la bibliothèque :
                    </label>
                    <select name="cover_existing" class="form-select form-select-sm" id="coverSelect">
                        <option value="">— Garder l'image actuelle / Aucune —</option>
                        <?php
                        $imgs = glob(IMAGES_DIR . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                        foreach ($imgs as $img):
                            $rel = 'assets/images/' . basename($img);
                        ?>
                        <option value="<?= esc($rel) ?>"
                            <?= ($ep['cover'] ?? '') === $rel ? 'selected' : '' ?>>
                            <?= esc(basename($img)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes de production -->
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-journal-text text-primary"></i> Notes de production
                </div>
                <textarea name="show_notes" class="form-control" rows="5"
                          placeholder="Points clés, notes Markdown..."><?= esc($ep['show_notes'] ?? '') ?></textarea>
                <div class="form-hint">Supporte le Markdown (** gras **, ## titre...)</div>
            </div>

            <!-- Boutons de soumission -->
            <div class="admin-form-card">
                <button type="submit" class="btn-admin-primary w-100 justify-content-center mb-2" style="padding:.75rem;">
                    <i class="bi bi-<?= $isEdit ? 'check2-circle' : 'plus-circle' ?> fs-5"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Publier l\'épisode' ?>
                </button>
                <p class="text-center text-muted small mb-0">
                    <i class="bi bi-file-earmark-code me-1"></i>
                    Écrit directement dans <code>data/episodes.json</code>
                </p>
                <?php if ($isEdit): ?>
                    <hr style="border-color:#EDF4FD;">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill"
                            onclick="confirmDelete(<?= (int)$ep['id'] ?>, '<?= esc(addslashes($ep['title'])) ?>')">
                        <i class="bi bi-trash3 me-1"></i> Supprimer cet épisode
                    </button>
                <?php endif; ?>
            </div>

        </div><!-- /col droite -->

    </div><!-- /row -->
</form>

<!-- Delete confirmation modal (réutilisé depuis index) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3" style="font-size:2rem;">🗑️</div>
                <h6 class="fw-bold mb-1" style="font-family:'Fredoka',sans-serif;color:#092A5C;">Supprimer cet épisode ?</h6>
                <p class="text-muted small mb-3" id="deleteModalTitle"></p>
                <form method="POST" action="delete.php">
                    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                    <input type="hidden" name="episode_id" id="deleteEpisodeId">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                            <i class="bi bi-trash3 me-1"></i>Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
