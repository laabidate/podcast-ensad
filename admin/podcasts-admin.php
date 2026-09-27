<?php
require_once __DIR__ . '/auth.php';

$pageTitle = 'Emissions';
$activeNav = 'podcasts';
$data = adminLoadData();
$podcasts = $data['podcasts'] ?? [];
$episodes = $data['episodes'] ?? [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$counts = [];
foreach ($episodes as $episode) {
    $id = (int)($episode['podcast_id'] ?? 0);
    if ($id) {
        $counts[$id] = ($counts[$id] ?? 0) + 1;
    }
}

require_once __DIR__ . '/layout-header.php';
?>

<?php if ($flash): ?>
    <div class="admin-alert <?= esc($flash['type']) ?>">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= esc($flash['msg']) ?>
    </div>
<?php endif; ?>

<div class="admin-table-wrap">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="border-color:#DDE9F8!important;">
        <div>
            <h5 class="fw-bold mb-0" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
                <i class="bi bi-broadcast-pin me-2 text-primary"></i>Emissions / Podcasts
            </h5>
            <span class="small text-muted"><?= count($podcasts) ?> emission(s) dans data/episodes.json</span>
        </div>
        <a href="podcast-form.php" class="btn-admin-primary">
            <i class="bi bi-plus-lg"></i> Ajouter
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:64px;">Cover</th>
                    <th>Titre</th>
                    <th>Categorie</th>
                    <th>Episodes</th>
                    <th style="width:110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($podcasts as $podcast): ?>
                <?php $id = (int)($podcast['id'] ?? 0); ?>
                <tr>
                    <td><img src="../<?= esc($podcast['cover'] ?? 'assets/images/micro-ensad-icon.svg') ?>" alt="" class="ep-thumb"></td>
                    <td class="ep-title-cell">
                        <div class="fw-semibold"><?= esc($podcast['title'] ?? '') ?></div>
                        <div class="small text-muted"><?= esc($podcast['hosts'] ?? '') ?></div>
                    </td>
                    <td><span class="ep-cat-badge"><?= esc($podcast['category'] ?? '') ?></span></td>
                    <td><span class="ep-id-badge"><?= $counts[$id] ?? 0 ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="podcast-form.php?id=<?= $id ?>" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="delete-podcast.php" onsubmit="return confirm('Supprimer cette emission ?');">
                                <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                                <input type="hidden" name="podcast_id" value="<?= $id ?>">
                                <button type="submit" class="btn-icon del" title="Supprimer"><i class="bi bi-trash3"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
