<?php
/**
 * API JSON : Liste et détails des épisodes
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $episode = getEpisodeById($id);
    if ($episode) {
        $episode['chapters'] = getChaptersByEpisodeId($episode['id']);
        echo json_encode(['status' => 'success', 'data' => $episode]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Épisode non trouvé']);
    }
    exit;
}

$limit     = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$podcastId = $_GET['podcast_id'] ?? null;
$category  = $_GET['cat'] ?? null;
$search    = $_GET['q'] ?? null;

$episodes = getEpisodes($limit, $podcastId, $category, $search);
echo json_encode([
    'status' => 'success',
    'total'  => count($episodes),
    'data'   => $episodes
]);
