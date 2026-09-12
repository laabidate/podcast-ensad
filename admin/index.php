<?php
/**
 * Admin Dashboard — Micro ENSAD Mohammedia
 * Tableau de bord principal : liste des épisodes + stats
 */
require_once __DIR__ . '/auth.php';

$pageTitle = 'Tableau de bord';
$activeNav = 'dashboard';

$data     = adminLoadData();
$episodes = $data['episodes'] ?? [];
$podcasts = $data['podcasts'] ?? [];

// Messages flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/layout-header.php';
?>

<!-- Flash messages -->
<?php if ($flash): ?>
    <div class="admin-alert <?= esc($flash['type']) ?>">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= esc($flash['msg']) ?>
    </div>
<?php endif; ?>

<!-- ── STAT CARDS ───────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EBF4FE;">
                <i class="bi bi-collection-play-fill" style="color:#1F5EAD;"></i>
            </div>
            <div>
                <div class="stat-num"><?= count($episodes) ?></div>
                <div class="stat-label">Épisodes publiés</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(255,229,43,0.2);">
                <i class="bi bi-broadcast" style="color:#927500;"></i>
            </div>
            <div>
                <div class="stat-num"><?= count($podcasts) ?></div>
                <div class="stat-label">Émissions / Séries</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#DCFCE7;">
                <i class="bi bi-youtube" style="color:#dc2626;"></i>
            </div>
            <div>
                <div class="stat-num"><?= count(array_filter($episodes, fn($e) => !empty($e['youtube_id']))) ?></div>
                <div class="stat-label">Avec audio YouTube</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#F3E8FF;">
                <i class="bi bi-people-fill" style="color:#7c3aed;"></i>
            </div>
            <div>
                <div class="stat-num"><?= count(array_unique(array_merge(...array_map(fn($e) => $e['contributors'] ?? [], $episodes)))) ?></div>
                <div class="stat-label">Étudiants contributeurs</div>
            </div>
        </div>
    </div>
<!-- ── QUICK ACTIONS & SVG LOGOS BANNER ────────── -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background:#FFFFFF; border-color:#DDE9F8!important;">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 text-center" style="background:#EBF4FE; width:46px; height:46px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-bezier2 fs-4 text-primary"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color:#092A5C;">Logos Vectoriels SVG</h6>
                    <small class="text-muted">Logo principal &amp; secondaire en haute fidélité</small>
                </div>
            </div>
            <a href="logos.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                Gérer les SVGs <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background:#FFFFFF; border-color:#DDE9F8!important;">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 text-center" style="background:#FEF9C3; width:46px; height:46px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-image fs-4 text-warning"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color:#092A5C;">Ajout d'Épisode &amp; Image</h6>
                    <small class="text-muted">Espace dédié pour uploader la pochette</small>
                </div>
            </div>
            <a href="episode-form.php" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">
                Nouvel Épisode <i class="bi bi-plus-lg ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background:#FFFFFF; border-color:#DDE9F8!important;">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 text-center" style="background:#E8F5E9; width:46px; height:46px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-house-gear fs-4" style="color:#2E7D32;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color:#092A5C;">Éditeur de la Page d'Accueil</h6>
                    <small class="text-muted">Hero, réseaux sociaux, CTA &amp; footer</small>
                </div>
            </div>
            <a href="home-editor.php" class="btn btn-sm btn-outline-success rounded-pill px-3">
                Modifier <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- ── EPISODES TABLE ────────────────────────────── -->
<div class="admin-table-wrap">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="border-color:#DDE9F8!important;">
        <div>
            <h5 class="fw-bold mb-0" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
                <i class="bi bi-collection-play me-2 text-primary"></i>Tous les Épisodes
            </h5>
            <span class="small text-muted"><?= count($episodes) ?> épisode(s) dans data/episodes.json</span>
        </div>
        <a href="episode-form.php" class="btn-admin-primary">
            <i class="bi bi-plus-lg"></i> Ajouter
        </a>
    </div>

    <?php if (empty($episodes)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p class="mb-0">Aucun épisode. <a href="episode-form.php">Ajoutez le premier !</a></p>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th style="width:64px;">Cover</th>
                    <th>Titre & Émission</th>
                    <th>Catégorie</th>
                    <th>YouTube ID</th>
                    <th>Durée</th>
                    <th style="width:110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($episodes as $ep): ?>
                <tr>
                    <td><span class="ep-id-badge"><?= esc($ep['ep_number'] ?? 'EP.'.$ep['id']) ?></span></td>
                    <td>
                        <img src="../<?= esc($ep['cover'] ?? 'assets/images/micro-ensad-icon.svg') ?>"
                             alt="" class="ep-thumb">
                    </td>
                    <td class="ep-title-cell">
                        <div class="fw-semibold" style="font-size:0.88rem;"><?= esc($ep['title']) ?></div>
                        <div class="small text-muted" style="font-size:0.72rem;"><?= esc($ep['podcast_title'] ?? '') ?></div>
                        <?php if (!empty($ep['contributors'])): ?>
                            <div style="font-size:0.7rem;color:#64748B;margin-top:3px;">
                                👥 <?= esc(implode(', ', array_slice($ep['contributors'], 0, 2))) ?>
                                <?= count($ep['contributors']) > 2 ? '...' : '' ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="ep-cat-badge"><?= esc($ep['podcast_category'] ?? '—') ?></span>
                    </td>
                    <td>
                        <?php if (!empty($ep['youtube_id'])): ?>
                            <span class="yt-id-chip"><?= esc($ep['youtube_id']) ?></span>
                        <?php else: ?>
                            <span class="yt-id-chip yt-missing">Manquant</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="font-monospace small"><?= esc($ep['duration'] ?? '—') ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="../episode-detail.php?id=<?= (int)$ep['id'] ?>" target="_blank"
                               class="btn-icon view" title="Voir sur le site">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="episode-form.php?id=<?= (int)$ep['id'] ?>"
                               class="btn-icon edit" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn-icon del"
                                    title="Supprimer"
                                    onclick="confirmDelete(<?= (int)$ep['id'] ?>, '<?= esc(addslashes($ep['title'])) ?>')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3" style="font-size:2.2rem;">🗑️</div>
                <h6 class="fw-bold mb-1" style="font-family:'Fredoka',sans-serif;color:#092A5C;">Supprimer cet épisode ?</h6>
                <p class="text-muted small mb-3" id="deleteModalTitle"></p>
                <div class="admin-alert info py-2 small mb-3">
                    <i class="bi bi-info-circle"></i>
                    Le fichier JSON sera modifié immédiatement.
                </div>
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
