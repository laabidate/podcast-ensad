<?php
/**
 * Admin Layout Header — Micro ENSAD
 * Inclure en tête de chaque page admin.
 * Variables attendues : $pageTitle, $activeNav
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($pageTitle ?? 'Admin') ?> — Micro ENSAD</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- ── SIDEBAR ────────────────────────────────────── -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-mic-fill"></i>
        </div>
        <div>
            <h6>MICRO ENSAD</h6>
            <small>Panneau d'administration</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Navigation</div>

        <a href="index.php" class="sidebar-link <?= ($activeNav === 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>

        <div class="sidebar-section-label">Épisodes</div>

        <a href="episode-form.php" class="sidebar-link <?= ($activeNav === 'add') ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i> Ajouter un épisode
        </a>
        <a href="index.php" class="sidebar-link <?= ($activeNav === 'list') ? 'active' : '' ?>">
            <i class="bi bi-collection-play"></i> Gérer les épisodes
        </a>

        <div class="sidebar-section-label">Identité & Médias</div>

        <a href="logos.php" class="sidebar-link <?= ($activeNav === 'logos') ? 'active' : '' ?>">
            <i class="bi bi-bezier2"></i> Logos SVG
        </a>
        <a href="home-editor.php" class="sidebar-link <?= ($activeNav === 'home-editor') ? 'active' : '' ?>">
            <i class="bi bi-house-gear"></i> Éditeur du Site
        </a>

        <div class="sidebar-section-label">Site</div>

        <a href="../index.php" class="sidebar-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Voir le site
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php">
            <i class="bi bi-box-arrow-left"></i> Se déconnecter
        </a>
    </div>
</aside>

<!-- ── MAIN ────────────────────────────────────────── -->
<div class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm border d-md-none" onclick="document.getElementById('adminSidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title"><?= esc($pageTitle ?? 'Admin') ?></span>
        </div>
        <div class="topbar-actions">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i') ?>
            </span>
            <a href="episode-form.php" class="btn-admin-yellow">
                <i class="bi bi-plus-lg"></i> Nouvel épisode
            </a>
        </div>
    </header>

    <div class="admin-content">
