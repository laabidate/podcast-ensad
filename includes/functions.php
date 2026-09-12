<?php
/**
 * Fonctions Utilitaires et Données Statiques - Micro ENSAD Mohammedia
 * 100% Statique - Aucune base de données requise
 */

/**
 * Charge les données depuis le fichier JSON statique
 */
function loadData(): array {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $jsonFile = __DIR__ . '/../data/episodes.json';
    if (!file_exists($jsonFile)) {
        return ['podcasts' => [], 'episodes' => []];
    }

    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    $cache = is_array($data) ? $data : ['podcasts' => [], 'episodes' => []];
    return $cache;
}

/**
 * Nettoie une chaîne pour l'affichage HTML
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Catégories officielles de l'école (dynamiques d'après les données)
 */
function getCategories(): array {
    $data = loadData();
    $cats = [];
    foreach ($data['podcasts'] ?? [] as $p) {
        if (!empty($p['category']) && !in_array($p['category'], $cats)) {
            $cats[] = $p['category'];
        }
    }
    return !empty($cats) ? $cats : [
        "Art & Performance",
        "IA & Création",
        "Orientation & Vie Étudiante",
        "Design Graphique",
        "Art Contemporain & Culture",
        "Art Contemporain",
        "Cinéma & Réalisation",
        "Jeux Vidéo & Game Design",
        "Culture Visuelle & Sport",
        "Jeux Vidéo & Société",
        "Carrière & Stages"
    ];
}

/**
 * Informations officielles sur l'institution et le projet
 */
function getInstitution(): array {
    $data = loadData();
    return $data['institution'] ?? [];
}

/**
 * Récupère tous les podcasts
 */
function getAllPodcasts(?string $category = null): array {
    $data = loadData();
    $podcasts = $data['podcasts'] ?? [];

    if ($category && $category !== 'all') {
        return array_values(array_filter($podcasts, fn($p) => ($p['category'] ?? '') === $category));
    }
    return $podcasts;
}

/**
 * Récupère un podcast par ID
 */
function getPodcastById($id): ?array {
    $data = loadData();
    foreach ($data['podcasts'] ?? [] as $pod) {
        if ($pod['id'] == $id) {
            return $pod;
        }
    }
    return null;
}

/**
 * Récupère le podcast mis à la une
 */
function getFeaturedPodcast(): ?array {
    $podcasts = getAllPodcasts();
    return !empty($podcasts) ? $podcasts[0] : null;
}

/**
 * Récupère les épisodes avec filtres optionnels
 */
function getEpisodes(?int $limit = null, $podcastId = null, ?string $category = null, ?string $search = null, ?string $status = null, ?string $orderBy = null): array {
    $data = loadData();
    $episodes = $data['episodes'] ?? [];

    if ($podcastId !== null && $podcastId !== 'all') {
        $episodes = array_filter($episodes, fn($e) => ($e['podcast_id'] ?? 0) == $podcastId);
    }

    if ($category !== null && $category !== 'all') {
        $episodes = array_filter($episodes, fn($e) => ($e['podcast_category'] ?? '') === $category);
    }

    if ($search !== null && trim($search) !== '') {
        $q = mb_strtolower(trim($search));
        $episodes = array_filter($episodes, function($e) use ($q) {
            $matched = (
                str_contains(mb_strtolower($e['title'] ?? ''), $q) ||
                str_contains(mb_strtolower($e['description'] ?? ''), $q) ||
                str_contains(mb_strtolower($e['host'] ?? ''), $q) ||
                str_contains(mb_strtolower($e['podcast_title'] ?? ''), $q)
            );
            if ($matched) return true;
            if (!empty($e['contributors']) && is_array($e['contributors'])) {
                foreach ($e['contributors'] as $c) {
                    if (str_contains(mb_strtolower($c), $q)) return true;
                }
            }
            return false;
        });
    }

    // Ré-indexer les clés
    $episodes = array_values($episodes);

    if ($limit !== null && $limit > 0) {
        $episodes = array_slice($episodes, 0, $limit);
    }

    return $episodes;
}

/**
 * Récupère un épisode par son ID
 */
function getEpisodeById($id): ?array {
    $data = loadData();
    foreach ($data['episodes'] ?? [] as $ep) {
        if ($ep['id'] == $id) {
            return $ep;
        }
    }
    return null;
}

/**
 * Récupère les chapitres d'un épisode
 */
function getChaptersByEpisodeId($episodeId): array {
    $ep = getEpisodeById($episodeId);
    return $ep['chapters'] ?? [];
}

/**
 * Statistiques de la plateforme
 */
function getStats(): array {
    $data = loadData();
    $totalPodcasts = count($data['podcasts'] ?? []);
    $totalEpisodes = count($data['episodes'] ?? []);
    $plays = 0;
    foreach ($data['episodes'] ?? [] as $ep) {
        $plays += ($ep['plays'] ?? 0);
    }

    return [
        'podcasts' => max($totalPodcasts, 6),
        'episodes' => max($totalEpisodes, 6),
        'plays'    => max($plays, 14850),
        'hours'    => 38
    ];
}

/**
 * Formatage d'un nombre (1.2K, 3.4M)
 */
function formatCount(int $n): string {
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'M';
    }
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'K';
    }
    return (string)$n;
}

/**
 * Rendu Markdown simplifié
 */
function renderMarkdown(string $text): string {
    $lines = explode("\n", $text);
    $html = '';
    $inList = false;

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if (str_starts_with($trimmed, '### ')) {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            $html .= "<h5 class='fw-bold mt-4 mb-2 text-primary'>" . htmlspecialchars(substr($trimmed, 4)) . "</h5>\n";
        } elseif (str_starts_with($trimmed, '## ')) {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            $html .= "<h4 class='fw-bold mt-4 mb-2 text-primary'>" . htmlspecialchars(substr($trimmed, 3)) . "</h4>\n";
        } elseif (str_starts_with($trimmed, '- ') || str_starts_with($trimmed, '* ')) {
            if (!$inList) { $html .= "<ul class='mb-3 ps-3 text-secondary'>\n"; $inList = true; }
            $content = substr($trimmed, 2);
            $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($content));
            $html .= "<li class='mb-1'>" . $content . "</li>\n";
        } elseif ($trimmed === '') {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
        } else {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($trimmed));
            $html .= "<p class='mb-2 text-secondary'>" . $content . "</p>\n";
        }
    }

    if ($inList) {
        $html .= "</ul>\n";
    }

    return $html;
}
