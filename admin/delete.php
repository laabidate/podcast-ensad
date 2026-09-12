<?php
/**
 * Delete Handler — Micro ENSAD Admin
 * Supprime un épisode du fichier episodes.json
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrfCheck();

$epId = (int)($_POST['episode_id'] ?? 0);
if (!$epId) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'ID épisode invalide.'];
    header('Location: index.php');
    exit;
}

$data     = adminLoadData();
$before   = count($data['episodes']);
$title    = '';

// Trouver le titre avant suppression (pour le message)
foreach ($data['episodes'] as $ep) {
    if ((int)$ep['id'] === $epId) { $title = $ep['title']; break; }
}

// Filtrer l'épisode
$data['episodes'] = array_values(array_filter(
    $data['episodes'],
    fn($e) => (int)$e['id'] !== $epId
));

if (count($data['episodes']) === $before) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => "Épisode #{$epId} introuvable."];
    header('Location: index.php');
    exit;
}

if (!adminSaveData($data)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => '❌ Erreur : impossible d\'écrire dans data/episodes.json.'];
    header('Location: index.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'msg' => "🗑️ Épisode « {$title} » supprimé de episodes.json."];
header('Location: index.php');
exit;
