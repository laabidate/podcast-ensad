<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: podcasts-admin.php');
    exit;
}

csrfCheck();

$podcastId = (int)($_POST['podcast_id'] ?? 0);
$data = adminLoadData();

$hasEpisodes = false;
foreach ($data['episodes'] ?? [] as $episode) {
    if ((int)($episode['podcast_id'] ?? 0) === $podcastId) {
        $hasEpisodes = true;
        break;
    }
}

if ($hasEpisodes) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Impossible de supprimer une emission qui contient des episodes.'];
    header('Location: podcasts-admin.php');
    exit;
}

$before = count($data['podcasts'] ?? []);
$data['podcasts'] = array_values(array_filter($data['podcasts'] ?? [], fn($podcast) => (int)($podcast['id'] ?? 0) !== $podcastId));

if (count($data['podcasts']) === $before) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Emission introuvable.'];
    header('Location: podcasts-admin.php');
    exit;
}

if (!adminSaveData($data)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Impossible d ecrire dans data/episodes.json.'];
    header('Location: podcasts-admin.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'msg' => 'Emission supprimee.'];
header('Location: podcasts-admin.php');
exit;
