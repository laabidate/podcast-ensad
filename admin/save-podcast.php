<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: podcasts-admin.php');
    exit;
}

csrfCheck();

$data = adminLoadData();
$podcasts = &$data['podcasts'];
$mode = $_POST['mode'] ?? 'add';
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$title || !$category || !$description) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Titre, categorie et description sont obligatoires.'];
    header('Location: podcast-form.php');
    exit;
}

$cover = trim($_POST['cover_existing'] ?? '');
$uploaded = adminUploadImage('cover_upload', 'podcast');
if ($uploaded) {
    $cover = $uploaded;
}

$payload = [
    'title' => $title,
    'hosts' => trim($_POST['hosts'] ?? ''),
    'category' => $category,
    'department' => trim($_POST['department'] ?? ''),
    'cover' => $cover ?: 'assets/images/micro-ensad-icon.svg',
    'description' => $description,
    'followers' => trim($_POST['followers'] ?? ''),
];

if ($mode === 'edit') {
    $podcastId = (int)($_POST['podcast_id'] ?? 0);
    $found = false;
    foreach ($podcasts as &$podcast) {
        if ((int)($podcast['id'] ?? 0) !== $podcastId) {
            continue;
        }
        $podcast = array_merge($podcast, $payload);
        foreach ($data['episodes'] as &$episode) {
            if ((int)($episode['podcast_id'] ?? 0) === $podcastId) {
                $episode['podcast_title'] = $title;
                $episode['podcast_category'] = $category;
                $episode['department'] = $payload['department'] ?: ($episode['department'] ?? '');
                if ($cover) {
                    $episode['cover'] = $cover;
                }
            }
        }
        unset($episode);
        $found = true;
        break;
    }
    unset($podcast);

    if (!$found) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Emission introuvable.'];
        header('Location: podcasts-admin.php');
        exit;
    }
    $message = "Emission \"{$title}\" modifiee.";
} else {
    $payload['id'] = adminNextPodcastId($data);
    $payload['episodes_count'] = 0;
    $podcasts[] = $payload;
    $message = "Emission \"{$title}\" ajoutee.";
}

if (!adminSaveData($data)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Impossible d ecrire dans data/episodes.json.'];
    header('Location: podcasts-admin.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'msg' => $message];
header('Location: podcasts-admin.php');
exit;
