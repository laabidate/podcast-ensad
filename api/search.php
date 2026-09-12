<?php
/**
 * API JSON : Recherche en direct multi-critères (100% Statique)
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/functions.php';

$query = trim($_GET['q'] ?? '');

if (mb_strlen($query) < 2) {
    echo json_encode(['status' => 'success', 'podcasts' => [], 'episodes' => []]);
    exit;
}

$q = mb_strtolower($query);
$data = loadData();

$foundPodcasts = [];
foreach ($data['podcasts'] ?? [] as $p) {
    if (
        str_contains(mb_strtolower($p['title'] ?? ''), $q) ||
        str_contains(mb_strtolower($p['description'] ?? ''), $q) ||
        str_contains(mb_strtolower($p['hosts'] ?? ''), $q)
    ) {
        $foundPodcasts[] = $p;
        if (count($foundPodcasts) >= 5) break;
    }
}

$foundEpisodes = [];
foreach ($data['episodes'] ?? [] as $e) {
    if (
        str_contains(mb_strtolower($e['title'] ?? ''), $q) ||
        str_contains(mb_strtolower($e['description'] ?? ''), $q) ||
        str_contains(mb_strtolower($e['host'] ?? ''), $q) ||
        str_contains(mb_strtolower($e['guest'] ?? ''), $q) ||
        str_contains(mb_strtolower($e['podcast_title'] ?? ''), $q)
    ) {
        $foundEpisodes[] = $e;
        if (count($foundEpisodes) >= 6) break;
    }
}

echo json_encode([
    'status'   => 'success',
    'query'    => $query,
    'podcasts' => $foundPodcasts,
    'episodes' => $foundEpisodes
]);
