<?php
/**
 * Build the public PHP site into a static dist/ folder.
 * Admin pages are intentionally not copied.
 */

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Cannot resolve project root.\n");
    exit(1);
}

$dist = $root . DIRECTORY_SEPARATOR . 'dist';
$siteUrl = getenv('SITE_URL') ?: '';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--site-url=')) {
        $siteUrl = rtrim(substr($arg, 11), '/');
    }
}

$data = json_decode(file_get_contents($root . '/data/episodes.json'), true);
$episodes = $data['episodes'] ?? [];
$podcasts = $data['podcasts'] ?? [];

removeDir($dist);
mkdir($dist, 0775, true);

$port = findFreePort();
$descriptor = [
    0 => ['pipe', 'r'],
    1 => ['file', $root . '/build-server.log', 'a'],
    2 => ['file', $root . '/build-server.log', 'a'],
];
$cmd = [PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', $root];
$process = proc_open($cmd, $descriptor, $pipes, $root);
if (!is_resource($process)) {
    fwrite(STDERR, "Cannot start local PHP server.\n");
    exit(1);
}

try {
    waitForServer($port);

    $pages = [
        ['welcome.php?preview=1', 'welcome.html'],
        ['index.php?skip_welcome=1', 'index.html'],
        ['episodes.php', 'episodes.html'],
        ['podcasts.php', 'podcasts.html'],
        ['about.php', 'about.html'],
    ];

    foreach ($episodes as $episode) {
        $id = (int)($episode['id'] ?? 0);
        if ($id > 0) {
            $pages[] = ['episode-detail.php?id=' . $id, 'episode-' . $id . '.html'];
        }
    }

    foreach ($podcasts as $podcast) {
        $id = (int)($podcast['id'] ?? 0);
        if ($id > 0) {
            $pages[] = ['podcast-detail.php?id=' . $id, 'podcast-' . $id . '.html'];
        }
    }

    foreach ($pages as [$source, $target]) {
        $html = fetchPage($port, $source);
        $html = rewriteHtml($html, $episodes, $podcasts, $siteUrl, $target);
        file_put_contents($dist . DIRECTORY_SEPARATOR . $target, $html);
        echo "Built {$target}\n";
    }

    copyDir($root . '/assets', $dist . '/assets');
    buildManifest($root, $dist);
    buildRobots($dist, $siteUrl);
    buildSitemap($dist, $siteUrl, $episodes, $podcasts);
    file_put_contents($dist . '/.nojekyll', '');
} finally {
    proc_terminate($process);
    proc_close($process);
}

echo "Static site ready in dist/\n";

function findFreePort(): int {
    for ($port = 8765; $port < 8795; $port++) {
        $socket = @stream_socket_server('tcp://127.0.0.1:' . $port, $errno, $errstr);
        if ($socket) {
            fclose($socket);
            return $port;
        }
    }
    throw new RuntimeException('No free local port found.');
}

function waitForServer(int $port): void {
    $deadline = microtime(true) + 10;
    while (microtime(true) < $deadline) {
        $result = @file_get_contents('http://127.0.0.1:' . $port . '/index.php?skip_welcome=1');
        if ($result !== false) {
            return;
        }
        usleep(150000);
    }
    throw new RuntimeException('Local PHP server did not become ready.');
}

function fetchPage(int $port, string $path): string {
    $url = 'http://127.0.0.1:' . $port . '/' . $path;
    $html = @file_get_contents($url);
    if ($html === false || trim($html) === '') {
        throw new RuntimeException('Failed to fetch ' . $path);
    }
    return $html;
}

function rewriteHtml(string $html, array $episodes, array $podcasts, string $siteUrl, string $target): string {
    foreach ($episodes as $episode) {
        $id = (int)($episode['id'] ?? 0);
        if ($id > 0) {
            $html = str_replace('episode-detail.php?id=' . $id, 'episode-' . $id . '.html', $html);
            $html = str_replace('/episode-detail.php?id=' . $id, '/episode-' . $id . '.html', $html);
        }
    }

    foreach ($podcasts as $podcast) {
        $id = (int)($podcast['id'] ?? 0);
        if ($id > 0) {
            $html = str_replace('podcast-detail.php?id=' . $id, 'podcast-' . $id . '.html', $html);
            $html = str_replace('/podcast-detail.php?id=' . $id, '/podcast-' . $id . '.html', $html);
        }
    }

    $replacements = [
        'index.php?skip_welcome=1' => 'index.html',
        'index.php?enter=1' => 'index.html',
        'index.php' => 'index.html',
        'welcome.php' => 'welcome.html',
        'episodes.php' => 'episodes.html',
        'podcasts.php' => 'podcasts.html',
        'about.php' => 'about.html',
    ];
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    if ($siteUrl !== '') {
        $html = preg_replace('~https?://micro-ensad\.ma~', $siteUrl, $html);
    }

    $canonicalUrl = $siteUrl !== '' ? $siteUrl . '/' . $target : $target;
    $html = preg_replace('~<link rel="canonical" href="[^"]*">~', '<link rel="canonical" href="' . htmlspecialchars($canonicalUrl, ENT_QUOTES) . '">', $html);
    $html = preg_replace('~<meta property="og:url" content="[^"]*">~', '<meta property="og:url" content="' . htmlspecialchars($canonicalUrl, ENT_QUOTES) . '">', $html);
    $html = preg_replace('~\s*<div class="preview-admin-bar">.*?</div>\s*~s', "\n", $html);

    return $html;
}

function buildManifest(string $root, string $dist): void {
    $manifestPath = $root . '/manifest.json';
    if (!is_file($manifestPath)) {
        return;
    }
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if (!is_array($manifest)) {
        return;
    }
    $manifest['start_url'] = 'index.html';
    $manifest['scope'] = './';
    foreach ($manifest['shortcuts'] ?? [] as &$shortcut) {
        if (isset($shortcut['url'])) {
            $shortcut['url'] = str_replace(['index.php', 'episodes.php', 'podcasts.php', 'about.php'], ['index.html', 'episodes.html', 'podcasts.html', 'about.html'], ltrim($shortcut['url'], '/'));
        }
    }
    unset($shortcut);
    file_put_contents($dist . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
}

function buildRobots(string $dist, string $siteUrl): void {
    $sitemap = $siteUrl !== '' ? $siteUrl . '/sitemap.xml' : 'sitemap.xml';
    file_put_contents($dist . '/robots.txt', "User-agent: *\nAllow: /\nSitemap: {$sitemap}\n");
}

function buildSitemap(string $dist, string $siteUrl, array $episodes, array $podcasts): void {
    if ($siteUrl === '') {
        file_put_contents($dist . '/sitemap.xml', "<!-- Set SITE_URL to generate absolute sitemap URLs. -->\n");
        return;
    }

    $urls = ['index.html', 'welcome.html', 'episodes.html', 'podcasts.html', 'about.html'];
    foreach ($episodes as $episode) {
        $id = (int)($episode['id'] ?? 0);
        if ($id > 0) $urls[] = 'episode-' . $id . '.html';
    }
    foreach ($podcasts as $podcast) {
        $id = (int)($podcast['id'] ?? 0);
        if ($id > 0) $urls[] = 'podcast-' . $id . '.html';
    }

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $url) {
        $xml .= "  <url><loc>" . htmlspecialchars($siteUrl . '/' . $url, ENT_XML1) . "</loc></url>\n";
    }
    $xml .= "</urlset>\n";
    file_put_contents($dist . '/sitemap.xml', $xml);
}

function copyDir(string $source, string $target): void {
    if (!is_dir($source)) return;
    if (!is_dir($target)) mkdir($target, 0775, true);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $dest = $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($dest)) mkdir($dest, 0775, true);
        } else {
            copy($item->getPathname(), $dest);
        }
    }
}

function removeDir(string $path): void {
    if (!is_dir($path)) return;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }
    rmdir($path);
}
