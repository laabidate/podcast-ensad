<?php
/**
 * Auth Guard — Micro ENSAD Admin
 * À inclure en tête de chaque page admin protégée.
 */
require_once __DIR__ . '/config.php';

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
    return is_array($data) ? $data : ['podcasts' => [], 'episodes' => []];
}

/**
 * Sauvegarde les données dans le fichier JSON.
 * @return bool
 */
function adminSaveData(array $data): bool {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(DATA_FILE, $json) !== false;
}

/**
 * Retourne le prochain ID disponible pour un épisode.
 */
function adminNextEpisodeId(array $data): int {
    if (empty($data['episodes'])) return 1;
    return max(array_column($data['episodes'], 'id')) + 1;
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
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        die('Token CSRF invalide.');
    }
}
