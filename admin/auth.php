<?php
/**
 * Auth Guard — Micro ENSAD Admin
 * À inclure en tête de chaque page admin protégée.
 */
require_once __DIR__ . '/config.php';

adminRequireLocal();
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Expiration de session
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > ADMIN_SESSION_DURATION) {
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}

// ------------------------------------------------------------------
// Fonctions utilitaires Admin
// ------------------------------------------------------------------

/**
 * Charge et retourne les données JSON complètes.
 */
function adminLoadData(): array {
    $content = file_get_contents(DATA_FILE);
    $data = json_decode($content, true);
    if (!is_array($data)) {
        return ['institution' => [], 'podcasts' => [], 'episodes' => []];
    }
    $data['podcasts'] = $data['podcasts'] ?? [];
    $data['episodes'] = $data['episodes'] ?? [];
    return $data;
}

/**
 * Sauvegarde les données dans le fichier JSON.
 * @return bool
 */
function adminSaveData(array $data): bool {
    adminSyncPodcastEpisodeCounts($data);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $backupDir = dirname(DATA_FILE) . '/backups';
    if (!is_dir($backupDir)) {
        @mkdir($backupDir, 0775, true);
    }
    if (is_file(DATA_FILE) && is_dir($backupDir)) {
        @copy(DATA_FILE, $backupDir . '/episodes-' . date('Ymd-His') . '.json');
    }

    $tmp = DATA_FILE . '.tmp';
    if (file_put_contents($tmp, $json . PHP_EOL, LOCK_EX) === false) {
        return false;
    }
    return rename($tmp, DATA_FILE);
}

/**
 * Retourne le prochain ID disponible pour un épisode.
 */
function adminNextEpisodeId(array $data): int {
    if (empty($data['episodes'])) return 1;
    return max(array_column($data['episodes'], 'id')) + 1;
}

function adminNextPodcastId(array $data): int {
    if (empty($data['podcasts'])) return 1;
    return max(array_map('intval', array_column($data['podcasts'], 'id'))) + 1;
}

function adminFindPodcast(array $data, int $podcastId): ?array {
    foreach ($data['podcasts'] ?? [] as $podcast) {
        if ((int)($podcast['id'] ?? 0) === $podcastId) {
            return $podcast;
        }
    }
    return null;
}

function adminSyncPodcastEpisodeCounts(array &$data): void {
    $counts = [];
    foreach ($data['episodes'] ?? [] as $episode) {
        $podcastId = (int)($episode['podcast_id'] ?? 0);
        if ($podcastId > 0) {
            $counts[$podcastId] = ($counts[$podcastId] ?? 0) + 1;
        }
    }

    foreach ($data['podcasts'] ?? [] as &$podcast) {
        $id = (int)($podcast['id'] ?? 0);
        $podcast['episodes_count'] = $counts[$id] ?? 0;
    }
    unset($podcast);
}

function adminUploadImage(string $fieldName, string $prefix = 'pod'): ?string {
    if (empty($_FILES[$fieldName]['name']) || !isset($_FILES[$fieldName]['tmp_name'])) {
        return null;
    }
    if (($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmp = $_FILES[$fieldName]['tmp_name'];
    $info = @getimagesize($tmp);
    if ($info === false) {
        return null;
    }

    $mimeMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $mime = $info['mime'] ?? '';
    if (!isset($mimeMap[$mime])) {
        return null;
    }

    $safe = preg_replace('/[^a-z0-9_-]/', '', strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_FILENAME)));
    $safe = $safe !== '' ? $safe : 'cover';
    $filename = $prefix . '_' . time() . '_' . $safe . '.' . $mimeMap[$mime];
    $dest = IMAGES_DIR . $filename;

    if (!move_uploaded_file($tmp, $dest)) {
        return null;
    }

    return 'assets/images/' . $filename;
}

/**
 * Échappe pour l'affichage HTML.
 */
function esc(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Convertit "MM:SS" → secondes
 */
function durationToSeconds(string $d): int {
    $parts = explode(':', trim($d));
    if (count($parts) === 2) return (int)$parts[0] * 60 + (int)$parts[1];
    if (count($parts) === 3) return (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
    return 0;
}

/**
 * Extrait l'ID YouTube depuis une URL ou retourne la valeur telle quelle.
 */
function extractYoutubeId(string $input): string {
    $input = trim($input);
    if (preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $input, $m)) {
        return $m[1];
    }
    return $input; // probablement déjà un ID
}

/**
 * Token CSRF simple
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}
function csrfCheck(): void {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        die('Token CSRF invalide.');
    }
}
