<?php
/**
 * Admin Login — Micro ENSAD Mohammedia
 */
require_once __DIR__ . '/config.php';
adminRequireLocal();
session_start();

// Déjà connecté → dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$expired = isset($_GET['expired']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    if ($pass === ADMIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in']  = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: index.php');
        exit;
    } else {
        $error = "Mot de passe incorrect. Réessayez.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Micro ENSAD</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
<style>
  body { background: #F6FAFE; display: flex; min-height: 100vh; align-items: center; justify-content: center; }
  .login-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(9,42,92,0.12);
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
    text-align: center;
  }
  .login-logo {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #1F5EAD, #092A5C);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    box-shadow: 0 8px 22px rgba(31,94,173,0.3);
  }
</style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
    </div>
    <h4 class="fw-bold mb-1" style="font-family:'Fredoka',sans-serif; color:#092A5C;">Espace Admin</h4>
    <p class="text-muted small mb-4">Micro ENSAD Mohammedia</p>

    <?php if ($expired): ?>
        <div class="alert alert-warning py-2 small rounded-3">
            <i class="bi bi-clock me-1"></i> Session expirée. Reconnectez-vous.
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 small rounded-3">
            <i class="bi bi-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3 text-start">
            <label class="form-label small fw-semibold text-dark">Mot de passe Admin</label>
            <div class="input-group">
                <input type="password" name="password" id="pwdInput"
                       class="form-control rounded-start-3"
                       placeholder="••••••••••" autofocus required>
                <button type="button" class="btn btn-outline-secondary rounded-end-3"
                        onclick="togglePwd()" title="Afficher/Masquer">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn w-100 fw-bold" style="background:#1F5EAD;color:#fff;border-radius:50px;padding:.7rem;">
            <i class="bi bi-unlock-fill me-2"></i> Accéder au panneau
        </button>
    </form>

    <div class="mt-4">
        <a href="../index.php" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Retour au site
        </a>
    </div>
</div>
<script>
function togglePwd() {
    const i = document.getElementById('pwdInput');
    const e = document.getElementById('eyeIcon');
    i.type = i.type === 'password' ? 'text' : 'password';
    e.className = i.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
