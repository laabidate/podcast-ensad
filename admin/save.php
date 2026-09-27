<?php
/**
 * Episode save handler - writes to data/episodes.json, no database.
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrfCheck();

$mode = $_POST['mode'] ?? 'add';
$data = adminLoadData();
$episodes = &$data['episodes'];

$title = trim($_POST['title'] ?? '');
$epNumber = trim($_POST['ep_number'] ?? '');
$date = trim($_POST['date'] ?? 'Semestre 2 - 2025/2026');
$description = trim($_POST['description'] ?? '');
$podcastId = (int)($_POST['podcast_id'] ?? 0);
$podcast = adminFindPodcast($data, $podcastId);
$filiere = trim($_POST['filiere'] ?? '');
$host = trim($_POST['host'] ?? '');
$showNotes = trim($_POST['show_notes'] ?? '');
$duration = trim($_POST['duration'] ?? '');
$durationSeconds = $duration ? durationToSeconds($duration) : 0;

$ytInput = trim($_POST['youtube_input'] ?? '');
$youtubeId = extractYoutubeId($ytInput);
$youtubeUrl = $youtubeId ? "https://www.youtube.com/watch?v={$youtubeId}" : '';

$contributors = array_values(array_filter(
    array_map('trim', $_POST['contributors'] ?? []),
    fn($name) => $name !== ''
));

$chapters = [];
$chapterTimes = $_POST['chapter_time'] ?? [];
$chapterTitles = $_POST['chapter_title'] ?? [];
for ($i = 0; $i < count($chapterTimes); $i++) {
    $time = trim($chapterTimes[$i] ?? '');
    $label = trim($chapterTitles[$i] ?? '');
    if ($time !== '' && $label !== '') {
        $chapters[] = [
            'time' => $time,
            'seconds' => durationToSeconds($time),
            'title' => $label,
        ];
    }
}

$cover = trim($_POST['cover_existing'] ?? '');
$uploadedCover = adminUploadImage('cover_upload', 'episode');
if ($uploadedCover) {
    $cover = $uploadedCover;
}

if (!$title || !$description || !$podcast || !$youtubeId) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Champs obligatoires manquants : titre, description, emission, YouTube ID.'];
    $back = ($mode === 'edit') ? 'episode-form.php?id=' . (int)($_POST['episode_id'] ?? 0) : 'episode-form.php';
    header('Location: ' . $back);
    exit;
}

$podcastTitle = $podcast['title'] ?? '';
$podcastCategory = $podcast['category'] ?? '';

if ($mode === 'edit') {
    $epId = (int)($_POST['episode_id'] ?? 0);
    $found = false;

    foreach ($episodes as &$episode) {
        if ((int)($episode['id'] ?? 0) !== $epId) {
            continue;
        }

        $episode['ep_number'] = $epNumber ?: ('EP. ' . str_pad((string)$epId, 2, '0', STR_PAD_LEFT));
        $episode['title'] = $title;
        $episode['date'] = $date;
        $episode['description'] = $description;
        $episode['podcast_id'] = $podcastId;
        $episode['podcast_title'] = $podcastTitle;
        $episode['podcast_category'] = $podcastCategory;
        $episode['filiere'] = $filiere;
        $episode['host'] = $host;
        $episode['show_notes'] = $showNotes;
        $episode['youtube_id'] = $youtubeId;
        $episode['youtube_url'] = $youtubeUrl;
        $episode['duration'] = $duration;
        $episode['duration_seconds'] = $durationSeconds;
        $episode['contributors'] = $contributors;
        $episode['chapters'] = $chapters;
        $episode['department'] = $podcast['department'] ?? ($episode['department'] ?? '');
        if ($cover) {
            $episode['cover'] = $cover;
        } elseif (!empty($podcast['cover'])) {
            $episode['cover'] = $podcast['cover'];
        }

        $found = true;
        break;
    }
    unset($episode);

    if (!$found) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => "Episode #{$epId} introuvable."];
        header('Location: index.php');
        exit;
    }

    $flashMsg = "Episode \"{$title}\" modifie avec succes.";
} else {
    $newId = adminNextEpisodeId($data);
    $epNum = $epNumber ?: ('EP. ' . str_pad((string)$newId, 2, '0', STR_PAD_LEFT));

    $episodes[] = [
        'id' => $newId,
        'ep_number' => $epNum,
        'podcast_id' => $podcastId,
        'podcast_title' => $podcastTitle,
        'podcast_category' => $podcastCategory,
        'filiere' => $filiere,
        'title' => $title,
        'host' => $host,
        'contributors' => $contributors,
        'department' => $podcast['department'] ?? 'DENSAD Bac+5',
        'encadrement' => 'Madame Randa El Amraoui (Module de Francais)',
        'cover' => $cover ?: ($podcast['cover'] ?? 'assets/images/micro-ensad-icon.svg'),
        'youtube_id' => $youtubeId,
        'youtube_url' => $youtubeUrl,
        'duration' => $duration,
        'duration_seconds' => $durationSeconds,
        'plays' => 0,
        'date' => $date,
        'description' => $description,
        'show_notes' => $showNotes,
        'chapters' => $chapters,
    ];

    $flashMsg = "Nouvel episode \"{$title}\" ajoute.";
}

if (!adminSaveData($data)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Impossible d ecrire dans data/episodes.json.'];
    header('Location: index.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'msg' => $flashMsg];
header('Location: index.php');
exit;
