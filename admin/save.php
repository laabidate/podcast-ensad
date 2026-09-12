<?php
/**
 * Save Handler — Micro ENSAD Admin
 * Reçoit le formulaire (add / edit) et écrit directement dans data/episodes.json
 * C'est ici que la "magie" se passe : pas de DB, juste file_put_contents().
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrfCheck();

$mode      = $_POST['mode'] ?? 'add';
$data      = adminLoadData();
$episodes  = &$data['episodes'];

// ── 1. Collecter et nettoyer les champs du formulaire ──────────────────

$title       = trim($_POST['title']      ?? '');
$ep_number   = trim($_POST['ep_number']  ?? '');
$date        = trim($_POST['date']       ?? 'Semestre 2 — 2025/2026');
$description = trim($_POST['description'] ?? '');
$podcast_title    = trim($_POST['podcast_title']    ?? '');
$podcast_category = trim($_POST['podcast_category'] ?? '');
$filiere     = trim($_POST['filiere']    ?? '');
$host        = trim($_POST['host']       ?? '');
$show_notes  = trim($_POST['show_notes'] ?? '');
$duration    = trim($_POST['duration']   ?? '');

// YouTube : extraire l'ID depuis URL ou ID brut
$yt_input  = trim($_POST['youtube_input'] ?? '');
$youtube_id = extractYoutubeId($yt_input);
$youtube_url = $youtube_id ? "https://www.youtube.com/watch?v={$youtube_id}" : '';

// Durée en secondes
$duration_seconds = $duration ? durationToSeconds($duration) : 0;

// Contributeurs : filtrer les vides
$contributors = array_values(array_filter(
    array_map('trim', $_POST['contributors'] ?? []),
    fn($c) => $c !== ''
));

// Chapitres
$chapter_times  = $_POST['chapter_time']  ?? [];
$chapter_titles = $_POST['chapter_title'] ?? [];
$chapters = [];
for ($i = 0; $i < count($chapter_times); $i++) {
    $t = trim($chapter_times[$i]  ?? '');
    $l = trim($chapter_titles[$i] ?? '');
    if ($t !== '' && $l !== '') {
        // Convertir MM:SS → secondes
        $parts = explode(':', $t);
        $secs  = count($parts) === 2
            ? (int)$parts[0] * 60 + (int)$parts[1]
            : (int)($parts[0] ?? 0) * 3600 + (int)($parts[1] ?? 0) * 60 + (int)($parts[2] ?? 0);
        $chapters[] = [
            'time'    => $t,
            'seconds' => $secs,
            'title'   => $l,
        ];
    }
}

// ── 2. Cover image ────────────────────────────────────────────────────

$cover = trim($_POST['cover_existing'] ?? '');

// Si un fichier est uploadé, il a la priorité
if (!empty($_FILES['cover_upload']['name'])) {
    $ext      = strtolower(pathinfo($_FILES['cover_upload']['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed)) {
        $safe     = preg_replace('/[^a-z0-9_\-]/', '', strtolower(pathinfo($_FILES['cover_upload']['name'], PATHINFO_FILENAME)));
        $filename = 'pod_' . time() . '_' . $safe . '.' . $ext;
        $dest     = IMAGES_DIR . $filename;
        if (move_uploaded_file($_FILES['cover_upload']['tmp_name'], $dest)) {
            $cover = 'assets/images/' . $filename;
        }
    }
}

// ── 3. Valider les champs obligatoires ────────────────────────────────

if (!$title || !$description || !$podcast_title || !$youtube_id) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Champs obligatoires manquants : titre, description, émission, YouTube ID.'];
    $back = ($mode === 'edit') ? 'episode-form.php?id=' . (int)($_POST['episode_id'] ?? 0) : 'episode-form.php';
    header('Location: ' . $back);
    exit;
}

// ── 4. Construire l'objet épisode ─────────────────────────────────────

if ($mode === 'edit') {
    // Trouver et mettre à jour
    $epId  = (int)($_POST['episode_id'] ?? 0);
    $found = false;

    foreach ($episodes as &$ep) {
        if ((int)$ep['id'] === $epId) {
            $epNum = $ep_number ?: ('EP. ' . str_pad($ep['id'], 2, '0', STR_PAD_LEFT));

            $ep['ep_number']        = $epNum;
            $ep['title']            = $title;
            $ep['date']             = $date;
            $ep['description']      = $description;
            $ep['podcast_title']    = $podcast_title;
            $ep['podcast_category'] = $podcast_category;
            $ep['filiere']          = $filiere;
            $ep['host']             = $host;
            $ep['show_notes']       = $show_notes;
            $ep['youtube_id']       = $youtube_id;
            $ep['youtube_url']      = $youtube_url;
            $ep['duration']         = $duration;
            $ep['duration_seconds'] = $duration_seconds;
            $ep['contributors']     = $contributors;
            $ep['chapters']         = $chapters;
            if ($cover) $ep['cover'] = $cover;

            $found = true;
            break;
        }
    }
    unset($ep);

    if (!$found) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => "Épisode #{$epId} introuvable pour la modification."];
        header('Location: index.php');
        exit;
    }

    $flashMsg = "✅ Épisode « {$title} » modifié avec succès dans episodes.json";

} else {
    // Ajouter un nouvel épisode
    $newId    = adminNextEpisodeId($data);
    $epNum    = $ep_number ?: ('EP. ' . str_pad($newId, 2, '0', STR_PAD_LEFT));

    $newEp = [
        'id'               => $newId,
        'ep_number'        => $epNum,
        'podcast_id'       => $newId,
        'podcast_title'    => $podcast_title,
        'podcast_category' => $podcast_category,
        'filiere'          => $filiere,
        'title'            => $title,
        'host'             => $host,
        'contributors'     => $contributors,
        'department'       => 'DENSAD Bac+5 — ' . ($filiere ?: 'Design Graphique & Interactif (DGI)'),
        'encadrement'      => 'Madame Randa El Amraoui (Module de Français)',
        'cover'            => $cover ?: 'assets/images/micro-ensad-icon.svg',
        'youtube_id'       => $youtube_id,
        'youtube_url'      => $youtube_url,
        'duration'         => $duration,
        'duration_seconds' => $duration_seconds,
        'plays'            => 0,
        'date'             => $date,
        'description'      => $description,
        'show_notes'       => $show_notes,
        'chapters'         => $chapters,
    ];

    $episodes[] = $newEp;
    $flashMsg   = "✅ Nouvel épisode « {$title} » ajouté (ID #{$newId}) dans episodes.json";
}

// ── 5. Écrire dans le fichier JSON ────────────────────────────────────

if (!adminSaveData($data)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => '❌ Erreur : impossible d\'écrire dans data/episodes.json. Vérifiez les permissions.'];
    header('Location: index.php');
    exit;
}

// ── 6. Rediriger avec message de succès ──────────────────────────────

$_SESSION['flash'] = ['type' => 'success', 'msg' => $flashMsg];
header('Location: index.php');
exit;
