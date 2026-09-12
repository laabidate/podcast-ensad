<?php
/**
 * Gestionnaire des Logos SVG — Micro ENSAD Admin
 * Permet de téléverser et modifier les logos officiels au format SVG (Principal & Secondaire)
 * 100% Statique : Enregistre directement dans assets/images/logo-*.svg
 */
require_once __DIR__ . '/auth.php';

$pageTitle = 'Gestion des Logos SVG';
$activeNav = 'logos';

$principalPath  = IMAGES_DIR . 'logo-principal.svg';
$secondairePath = IMAGES_DIR . 'logo-secondaire.svg';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();

    $logoType = $_POST['logo_type'] ?? '';
    $action   = $_POST['action'] ?? 'upload';

    if ($logoType === 'principal') {
        $targetFile = $principalPath;
        $labelName  = 'Logo Principal';
    } elseif ($logoType === 'secondaire') {
        $targetFile = $secondairePath;
        $labelName  = 'Logo Secondaire';
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Type de logo non reconnu.'];
        header('Location: logos.php');
        exit;
    }

    if ($action === 'upload') {
        if (!isset($_FILES['logo_file']) || $_FILES['logo_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Veuillez sélectionner un fichier SVG valide.'];
            header('Location: logos.php');
            exit;
        }

        $fileName = $_FILES['logo_file']['name'];
        $tmpName  = $_FILES['logo_file']['tmp_name'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext !== 'svg') {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Format invalide : Seuls les fichiers .svg sont autorisés.'];
            header('Location: logos.php');
            exit;
        }

        $content = file_get_contents($tmpName);

        // Validation du contenu SVG
        if (!str_contains($content, '<svg') || !str_contains($content, '</svg>')) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Le fichier uploadé ne contient pas de balises SVG valides.'];
            header('Location: logos.php');
            exit;
        }

        // Sauvegarde directe
        if (file_put_contents($targetFile, $content) !== false) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Le {$labelName} (SVG) a été mis à jour avec succès !"];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => "Erreur lors de l'écriture du fichier SVG."];
        }

        header('Location: logos.php');
        exit;
    } elseif ($action === 'code') {
        $svgCode = trim($_POST['svg_code'] ?? '');
        if (!str_contains($svgCode, '<svg') || !str_contains($svgCode, '</svg>')) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Le code saisi ne contient pas de balises <svg> valides.'];
            header('Location: logos.php');
            exit;
        }

        if (file_put_contents($targetFile, $svgCode) !== false) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Le code source du {$labelName} a été enregistré avec succès !"];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => "Erreur lors de l'enregistrement du code SVG."];
        }

        header('Location: logos.php');
        exit;
    }
}

// Données actuelles
$principalExists  = file_exists($principalPath);
$secondaireExists = file_exists($secondairePath);

$principalContent  = $principalExists ? file_get_contents($principalPath) : '';
$secondaireContent = $secondaireExists ? file_get_contents($secondairePath) : '';

$principalSize  = $principalExists ? round(filesize($principalPath) / 1024, 1) . ' Ko' : 'Inexistant';
$secondaireSize = $secondaireExists ? round(filesize($secondairePath) / 1024, 1) . ' Ko' : 'Inexistant';

$principalMtime  = $principalExists ? date('d/m/Y H:i', filemtime($principalPath)) : '—';
$secondaireMtime = $secondaireExists ? date('d/m/Y H:i', filemtime($secondairePath)) : '—';

// Messages flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/layout-header.php';
?>

<!-- Flash messages -->
<?php if ($flash): ?>
    <div class="admin-alert <?= esc($flash['type']) ?> mb-4">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= esc($flash['msg']) ?>
    </div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="font-family:'Fredoka',sans-serif;color:#092A5C;">
            <i class="bi bi-bezier2 me-2 text-primary"></i>Gestion des Logos Vectoriels (SVG)
        </h4>
        <p class="text-muted small mb-0">
            Téléversez vos fichiers SVG officiels. Ils sont immédiatement reflétés sur le site sans perte de qualité.
        </p>
    </div>
    <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
        <i class="bi bi-box-arrow-up-right me-1"></i> Voir sur le site
    </a>
</div>

<div class="row g-4">

    <!-- ── 1. LOGO PRINCIPAL (SVG) ────────────────── -->
    <div class="col-12 col-xl-6">
        <div class="admin-form-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3" style="border-color:#DDE9F8;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-2 py-1 rounded font-monospace" style="font-size:0.75rem;">PRINCIPAL</span>
                    <h6 class="fw-bold mb-0" style="color:#092A5C;">Logo Principal (Header &amp; Footer)</h6>
                </div>
                <span class="badge bg-light text-muted border font-monospace"><?= $principalSize ?></span>
            </div>

            <!-- Double aperçu : Fond Sombre & Fond Clair -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#050B14; border:1px solid #162C4E; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="text-muted small d-block mb-2" style="font-size:0.68rem;">Aperçu Mode Sombre</span>
                        <?php if ($principalExists): ?>
                            <img src="../assets/images/logo-principal.svg?v=<?= time() ?>" alt="Logo Principal Sombre" style="max-height:46px; max-width:100%; object-fit:contain;">
                        <?php else: ?>
                            <span class="text-muted small italic">Aucun SVG</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#FFFFFF; border:1px solid #E2E8F0; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="text-muted small d-block mb-2" style="font-size:0.68rem;">Aperçu Mode Clair</span>
                        <?php if ($principalExists): ?>
                            <img src="../assets/images/logo-principal.svg?v=<?= time() ?>" alt="Logo Principal Clair" style="max-height:46px; max-width:100%; object-fit:contain;">
                        <?php else: ?>
                            <span class="text-muted small italic">Aucun SVG</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="small text-muted mb-3 d-flex justify-content-between">
                <span>Fichier cible : <code>assets/images/logo-principal.svg</code></span>
                <span>Modifié le : <?= $principalMtime ?></span>
            </div>

            <!-- Formulaire d'upload SVG -->
            <form method="POST" action="logos.php" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                <input type="hidden" name="logo_type" value="principal">
                <input type="hidden" name="action" value="upload">

                <label class="form-label fw-semibold">Uploader un nouveau logo SVG</label>
                <div class="input-group mb-2">
                    <input type="file" name="logo_file" class="form-control" accept=".svg,image/svg+xml" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Remplacer
                    </button>
                </div>
                <div class="form-hint">Sélectionnez votre fichier vectoriel .svg créé sous Illustrator, Figma ou Inkscape.</div>
            </form>

            <!-- Code Source Direct SVG -->
            <details class="mt-2">
                <summary class="small text-primary fw-semibold" style="cursor:pointer;">
                    <i class="bi bi-code-slash me-1"></i> Voir ou modifier le code source XML SVG
                </summary>
                <form method="POST" action="logos.php" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                    <input type="hidden" name="logo_type" value="principal">
                    <input type="hidden" name="action" value="code">
                    <textarea name="svg_code" class="form-control font-monospace small mb-2" rows="6" style="font-size:0.75rem;"><?= esc($principalContent) ?></textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-check2 me-1"></i> Enregistrer le code SVG
                    </button>
                </form>
            </details>
        </div>
    </div>

    <!-- ── 2. LOGO SECONDAIRE (SVG) ────────────────── -->
    <div class="col-12 col-xl-6">
        <div class="admin-form-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3" style="border-color:#DDE9F8;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2 py-1 rounded font-monospace" style="font-size:0.75rem;">SECONDAIRE</span>
                    <h6 class="fw-bold mb-0" style="color:#092A5C;">Logo Secondaire (Icône / Badge)</h6>
                </div>
                <span class="badge bg-light text-muted border font-monospace"><?= $secondaireSize ?></span>
            </div>

            <!-- Double aperçu : Fond Sombre & Fond Clair -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#050B14; border:1px solid #162C4E; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="text-muted small d-block mb-2" style="font-size:0.68rem;">Aperçu Mode Sombre</span>
                        <?php if ($secondaireExists): ?>
                            <img src="../assets/images/logo-secondaire.svg?v=<?= time() ?>" alt="Logo Secondaire Sombre" style="max-height:46px; max-width:100%; object-fit:contain;">
                        <?php else: ?>
                            <span class="text-muted small italic">Aucun SVG</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#FFFFFF; border:1px solid #E2E8F0; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="text-muted small d-block mb-2" style="font-size:0.68rem;">Aperçu Mode Clair</span>
                        <?php if ($secondaireExists): ?>
                            <img src="../assets/images/logo-secondaire.svg?v=<?= time() ?>" alt="Logo Secondaire Clair" style="max-height:46px; max-width:100%; object-fit:contain;">
                        <?php else: ?>
                            <span class="text-muted small italic">Aucun SVG</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="small text-muted mb-3 d-flex justify-content-between">
                <span>Fichier cible : <code>assets/images/logo-secondaire.svg</code></span>
                <span>Modifié le : <?= $secondaireMtime ?></span>
            </div>

            <!-- Formulaire d'upload SVG -->
            <form method="POST" action="logos.php" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                <input type="hidden" name="logo_type" value="secondaire">
                <input type="hidden" name="action" value="upload">

                <label class="form-label fw-semibold">Uploader un nouveau logo secondaire SVG</label>
                <div class="input-group mb-2">
                    <input type="file" name="logo_file" class="form-control" accept=".svg,image/svg+xml" required>
                    <button class="btn btn-warning text-dark fw-bold" type="submit">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Remplacer
                    </button>
                </div>
                <div class="form-hint">Badge compact, icône de microphone ou favicon vectorielle.</div>
            </form>

            <!-- Code Source Direct SVG -->
            <details class="mt-2">
                <summary class="small text-primary fw-semibold" style="cursor:pointer;">
                    <i class="bi bi-code-slash me-1"></i> Voir ou modifier le code source XML SVG
                </summary>
                <form method="POST" action="logos.php" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= esc(csrfToken()) ?>">
                    <input type="hidden" name="logo_type" value="secondaire">
                    <input type="hidden" name="action" value="code">
                    <textarea name="svg_code" class="form-control font-monospace small mb-2" rows="6" style="font-size:0.75rem;"><?= esc($secondaireContent) ?></textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-check2 me-1"></i> Enregistrer le code SVG
                    </button>
                </form>
            </details>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
