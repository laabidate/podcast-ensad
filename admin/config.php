<?php
/**
 * Admin Config - Micro ENSAD Mohammedia
 * The admin area is intended for local editing before generating the static site.
 */

$envPassword = getenv('MICRO_ENSAD_ADMIN_PASSWORD');
define('ADMIN_PASSWORD', $envPassword !== false && $envPassword !== '' ? $envPassword : 'ensad2026');
define('ADMIN_SESSION_DURATION', 7200);
define('DATA_FILE', __DIR__ . '/../data/episodes.json');
define('IMAGES_DIR', __DIR__ . '/../assets/images/');
define('SITE_ROOT', __DIR__ . '/../');

function adminIsLocalRequest(): bool {
    if (PHP_SAPI === 'cli') {
        return true;
    }

    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    $host = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
    $isLocalHost = false;

    foreach (['localhost', '127.0.0.1', '[::1]', '::1'] as $allowed) {
        if ($host === $allowed || str_starts_with($host, $allowed . ':')) {
            $isLocalHost = true;
            break;
        }
    }

    return in_array($remote, ['127.0.0.1', '::1'], true) && $isLocalHost;
}

function adminRequireLocal(): void {
    if (adminIsLocalRequest() || getenv('MICRO_ENSAD_ALLOW_REMOTE_ADMIN') === '1') {
        return;
    }

    http_response_code(403);
    echo 'Admin local uniquement. Ouvrez le site via http://localhost ou http://127.0.0.1 sur votre ordinateur.';
    exit;
}
