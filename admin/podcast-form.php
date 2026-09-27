<?php
require_once __DIR__ . '/auth.php';

$data = adminLoadData();
$podcasts = $data['podcasts'] ?? [];
$podcastId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = false;
$podcast = [];

if ($podcastId) {
    foreach ($podcasts as $item) {
        if ((int)($item['id'] ?? 0) === $podcastId) {
            $podcast = $item;
            $isEdit = true;
            break;
        }
    }
    if (!$isEdit) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => "Emission #{$podcastId} introuvable."];
        header('Location: podcasts-admin.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Modifier emission' : 'Ajouter emission';
$activeNav = 'podcasts';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="podcasts-admin.php" class="btn-icon" style="color:#64748B;" title="Retour">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
            <i class="bi bi-broadcast-pin me-2 text-primary"></i><?= $isEdit ? 'Modifier emission' : 'Nouvelle emission' ?>
        </h5>
        <span class="small text-muted">Toutes les donnees sont enregistrees dans data/episodes.json.</span>
    </div>
</div>

<form method="POST" action="save-podcast.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
    <input type="hidden" name="mode" value="<?= $isEdit ? 'edit' : 'add' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="podcast_id" value="<?= (int)$podcast['id'] ?>">
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-info-circle-fill text-primary"></i> Informations emission
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= esc($podcast['title'] ?? '') ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Categorie <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" value="<?= esc($podcast['category'] ?? '') ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Filiere / Departement</label>
                        <input type="text" name="department" class="form-control" value="<?= esc($podcast['department'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Animateurs</label>
                        <input type="text" name="hosts" class="form-control" value="<?= esc($podcast['hosts'] ?? '') ?>" placeholder="Nom 1, Nom 2, Nom 3">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="5" required><?= esc($podcast['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Followers</label>
                        <input type="text" name="followers" class="form-control" value="<?= esc($podcast['followers'] ?? '') ?>" placeholder="Ex : 2.4K">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="admin-form-card mb-4">
                <div class="form-section-title">
                    <i class="bi bi-image text-primary"></i> Couverture
                </div>
                <div class="admin-preview-box mb-3">
                    <img src="../<?= esc($podcast['cover'] ?? 'assets/images/micro-ensad-icon.svg') ?>" alt="" class="admin-preview-img">
                </div>
                <label class="form-label">Importer une image</label>
                <input type="file" name="cover_upload" class="form-control mb-3" accept="image/jpeg,image/png,image/gif,image/webp">
                <label class="form-label">Ou choisir une image existante</label>
                <select name="cover_existing" class="form-select">
                    <option value="">-- Garder / aucune --</option>
                    <?php foreach (glob(IMAGES_DIR . '*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE) as $img): ?>
                        <?php $rel = 'assets/images/' . basename($img); ?>
                        <option value="<?= esc($rel) ?>" <?= ($podcast['cover'] ?? '') === $rel ? 'selected' : '' ?>>
                            <?= esc(basename($img)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="admin-form-card">
                <button type="submit" class="btn-admin-primary w-100 justify-content-center mb-2" style="padding:.75rem;">
                    <i class="bi bi-check2-circle fs-5"></i> Enregistrer
                </button>
                <p class="text-center text-muted small mb-0">Sauvegarde locale, sans base de donnees.</p>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
