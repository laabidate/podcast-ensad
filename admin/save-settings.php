<?php
/**
 * Save Settings — Admin Micro ENSAD
 * Reçoit les données POST de home-editor.php et les écrit dans data/site-settings.json
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home-editor.php');
    exit;
}

// CSRF Check
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== csrfToken()) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Token CSRF invalide. Veuillez réessayer.'];
    header('Location: home-editor.php');
    exit;
}

$settingsFile = __DIR__ . '/../data/site-settings.json';

// Load existing settings
$settings = [];
if (file_exists($settingsFile)) {
    $raw = file_get_contents($settingsFile);
    $settings = json_decode($raw, true) ?? [];
}

// Helper: sanitize text input
function sanitizeText(string $value, int $maxLen = 500): string {
    return mb_substr(trim(strip_tags($value)), 0, $maxLen);
}

// Helper: sanitize URL
function sanitizeUrl(string $value): string {
    $v = trim($value);
    if (empty($v)) return '';
    // Allow relative paths too
    if (str_starts_with($v, 'assets/')) return $v;
    // Only allow http/https
    if (!preg_match('/^https?:\/\//', $v)) return '';
    return filter_var($v, FILTER_SANITIZE_URL) ?: '';
}

// Helper: sanitize email
function sanitizeEmail(string $value): string {
    return filter_var(trim($value), FILTER_SANITIZE_EMAIL) ?: '';
}

// ── WELCOME PAGE SECTION ──
if (!isset($settings['welcome_page'])) {
    $settings['welcome_page'] = [];
}
$settings['welcome_page']['enabled']        = isset($_POST['welcome_enabled']);
$settings['welcome_page']['title']          = sanitizeText($_POST['welcome_title'] ?? 'Bienvenue sur Micro ENSAD', 120);
$settings['welcome_page']['subtitle']       = sanitizeText($_POST['welcome_subtitle'] ?? 'La Voix Créative de l\'ENSAD Casablanca', 150);
$settings['welcome_page']['badge_text']     = sanitizeText($_POST['welcome_badge_text'] ?? 'EXPÉRIENCE IMMERSIVE & AUDIOVISUELLE', 100);
$settings['welcome_page']['description']    = sanitizeText($_POST['welcome_description'] ?? '', 800);
$settings['welcome_page']['video_url']      = sanitizeUrl($_POST['welcome_video_url'] ?? '');
$settings['welcome_page']['cta_start_text'] = sanitizeText($_POST['welcome_cta_start_text'] ?? 'Commencer l\'expérience', 80);
$settings['welcome_page']['allow_skip']     = isset($_POST['welcome_allow_skip']);

// Poster image handling: URL or uploaded file
$posterExisting = sanitizeUrl($_POST['welcome_poster_url'] ?? '') ?: ($settings['welcome_page']['poster_image'] ?? 'assets/images/HeaderBG.png');

if (!empty($_FILES['welcome_poster_file']['name']) && $_FILES['welcome_poster_file']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['welcome_poster_file']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (in_array($ext, $allowed)) {
        $safeName = preg_replace('/[^a-z0-9_\-]/', '', strtolower(pathinfo($_FILES['welcome_poster_file']['name'], PATHINFO_FILENAME)));
        $filename = 'welcome_poster_' . time() . '_' . substr($safeName, 0, 30) . '.' . $ext;
        $dest = IMAGES_DIR . $filename;
        if (move_uploaded_file($_FILES['welcome_poster_file']['tmp_name'], $dest)) {
            $posterExisting = 'assets/images/' . $filename;
        }
    }
}
$settings['welcome_page']['poster_image'] = $posterExisting;

// ── RANDA INTRO (MOT D'OUVERTURE DE L'ENSEIGNANTE) ──
if (!isset($settings['randa_intro'])) {
    $settings['randa_intro'] = [];
}
$settings['randa_intro']['enabled']         = isset($_POST['randa_enabled']);
$settings['randa_intro']['show_in_welcome'] = isset($_POST['randa_show_welcome']);
$settings['randa_intro']['show_in_hero']    = isset($_POST['randa_show_hero']);
$settings['randa_intro']['name']            = sanitizeText($_POST['randa_name'] ?? 'Mme Randa El Amraoui', 120);
$settings['randa_intro']['role']            = sanitizeText($_POST['randa_role'] ?? 'Responsable du Projet • Module de Français (Bac+5)', 150);
$settings['randa_intro']['title']           = sanitizeText($_POST['randa_title'] ?? 'Mot de Présentation du Projet', 150);
$settings['randa_intro']['quote']           = sanitizeText($_POST['randa_quote'] ?? '', 800);

// Audio file upload or URL
$randaAudioExisting = sanitizeUrl($_POST['randa_audio_url'] ?? '') ?: ($settings['randa_intro']['audio_url'] ?? '');
if (!empty($_FILES['randa_audio_file']['name']) && $_FILES['randa_audio_file']['error'] === UPLOAD_ERR_OK) {
    $audioDir = __DIR__ . '/../assets/audio/';
    if (!is_dir($audioDir)) {
        @mkdir($audioDir, 0777, true);
    }
    $ext = strtolower(pathinfo($_FILES['randa_audio_file']['name'], PATHINFO_EXTENSION));
    $allowedAudio = ['mp3', 'wav', 'm4a', 'ogg', 'aac', 'weba'];
    if (in_array($ext, $allowedAudio)) {
        $safeName = preg_replace('/[^a-z0-9_\-]/', '', strtolower(pathinfo($_FILES['randa_audio_file']['name'], PATHINFO_FILENAME)));
        $filename = 'randa_intro_' . time() . '_' . substr($safeName, 0, 30) . '.' . $ext;
        $dest = $audioDir . $filename;
        if (move_uploaded_file($_FILES['randa_audio_file']['tmp_name'], $dest)) {
            $randaAudioExisting = 'assets/audio/' . $filename;
        }
    }
}
$settings['randa_intro']['audio_url'] = $randaAudioExisting;

// Avatar image upload or URL
$randaAvatarExisting = sanitizeUrl($_POST['randa_avatar_url'] ?? '') ?: ($settings['randa_intro']['avatar_image'] ?? 'assets/images/micro-ensad-icon.svg');
if (!empty($_FILES['randa_avatar_file']['name']) && $_FILES['randa_avatar_file']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['randa_avatar_file']['name'], PATHINFO_EXTENSION));
    $allowedImg = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    if (in_array($ext, $allowedImg)) {
        $safeName = preg_replace('/[^a-z0-9_\-]/', '', strtolower(pathinfo($_FILES['randa_avatar_file']['name'], PATHINFO_FILENAME)));
        $filename = 'randa_avatar_' . time() . '_' . substr($safeName, 0, 30) . '.' . $ext;
        $dest = IMAGES_DIR . $filename;
        if (move_uploaded_file($_FILES['randa_avatar_file']['tmp_name'], $dest)) {
            $randaAvatarExisting = 'assets/images/' . $filename;
        }
    }
}
$settings['randa_intro']['avatar_image'] = $randaAvatarExisting;

// ── HERO SECTION ──
$settings['hero']['badge_text']        = sanitizeText($_POST['hero_badge_text'] ?? '', 100);
$settings['hero']['title']             = sanitizeText($_POST['hero_title'] ?? '', 100);
$settings['hero']['subtitle']          = sanitizeText($_POST['hero_subtitle'] ?? '', 100);
$settings['hero']['description']       = sanitizeText($_POST['hero_description'] ?? '', 600);
$settings['hero']['cta_listen_text']   = sanitizeText($_POST['hero_cta_listen'] ?? '', 80);
$settings['hero']['cta_discover_text'] = sanitizeText($_POST['hero_cta_discover'] ?? '', 80);

// ── CTA BANNER ──
$settings['cta_banner']['tag_text']        = sanitizeText($_POST['cta_tag_text'] ?? '', 80);
$settings['cta_banner']['title']           = sanitizeText($_POST['cta_title'] ?? '', 120);
$settings['cta_banner']['subtitle']        = sanitizeText($_POST['cta_subtitle'] ?? '', 80);
$settings['cta_banner']['description']     = sanitizeText($_POST['cta_description'] ?? '', 600);
$settings['cta_banner']['cta_text']        = sanitizeText($_POST['cta_cta_text'] ?? '', 80);
$settings['cta_banner']['contact_email']   = sanitizeEmail($_POST['cta_contact_email'] ?? '');
$settings['cta_banner']['handwritten_note']= sanitizeText($_POST['cta_handwritten_note'] ?? '', 60);

// ── SOCIAL MEDIA ──
$socialKeys = ['instagram', 'youtube', 'linkedin', 'spotify', 'facebook', 'tiktok'];
foreach ($socialKeys as $key) {
    $settings['social'][$key]['url']     = sanitizeUrl($_POST['social_' . $key . '_url'] ?? '');
    $settings['social'][$key]['label']   = sanitizeText($_POST['social_' . $key . '_label'] ?? '', 80);
    $settings['social'][$key]['enabled'] = isset($_POST['social_' . $key . '_enabled']);
}

// ── FOOTER ──
$settings['footer']['institution_name'] = sanitizeText($_POST['footer_institution'] ?? '', 200);
$settings['footer']['university_name']  = sanitizeText($_POST['footer_university'] ?? '', 200);
$settings['footer']['podcast_tagline']  = sanitizeText($_POST['footer_tagline'] ?? '', 100);
$settings['footer']['copyright_name']  = sanitizeText($_POST['footer_copyright'] ?? '', 100);

// ── NAVBAR ──
$settings['navbar']['brand_title']    = sanitizeText($_POST['navbar_brand_title'] ?? '', 60);
$settings['navbar']['brand_subtitle'] = sanitizeText($_POST['navbar_brand_subtitle'] ?? '', 80);
$settings['navbar']['listen_btn_text']= sanitizeText($_POST['navbar_listen_btn'] ?? '', 40);

// Write to file
$jsonOutput = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (file_put_contents($settingsFile, $jsonOutput) === false) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Erreur: impossible d\'écrire dans data/site-settings.json. Vérifiez les permissions du fichier.'];
} else {
    $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Paramètres du site enregistrés avec succès !'];
}

header('Location: home-editor.php');
exit;
