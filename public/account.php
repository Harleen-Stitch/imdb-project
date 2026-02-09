<?php 
// public/account.php
declare(strict_types=1);

// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/security.php';
require_once dirname(__DIR__) . '/src/account_function.php';
startSecureSession();
getCurrentUser();

$user = getCurrentUser();
$userId = (int)($user['user_id'] ?? 0);
if ($userId <= 0) {
    header('Location: ' . APP_URL . '/public/login.php');
    exit;
}

$error = null;
// Soumission du formulaire de suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = "Une erreur est survenue";
        error_log("CSRF détecté sur delete_account pour user {$userId}");
    } else {
        try {
            deleteUserById($userId);

            // Déconnexion manuelle pour pouvoir passer un message via l'URL (car logout() détruit la session)
            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }

            // Redirection vers l'accueil avec encart
            $_SESSION['flash_success'] = 'Compte supprimé';
            header('Location: ' . APP_URL . '/public/index.php');
            exit;
        } catch (Throwable $e) {
            $error = "Impossible de supprimer votre compte pour le moment.";
            error_log("Delete account error for user {$userId}: " . $e->getMessage());
        }
    }
}

include dirname(__DIR__) . '/includes/header.php';

$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']); // encart ne s'affiche qu'une fois
?>
<main class="container" style="max-width: 900px; margin-inline: auto;">
    <?php if (!empty($flashSuccess)) : ?>
        <div id="order-modal" class="modal" aria-hidden="false" role="dialog" aria-labelledBy="order-modal-title">
            <div class="modal-content">
                <h3 id="order-modal-titel">Compte supprimé !</h3>
                <p> Votre compte a été supprimé avec succès.</p>

            </div>
        </div>
        <?php endif; ?>
    <h1>Mon compte</h1>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="card" style="padding:1rem; margin-top:1rem;">
        <h2>Informations du profil :</h2>
        <dl>
            <dt class="bold">Nom d'utilisateur :</dt>
            <dd>• <?= htmlspecialchars($user['username'] ?? '') ?></dd>

            <dt class="bold">Email :</dt>
            <dd>• <?= htmlspecialchars($user['email'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card" style="padding:1rem; margin-top:1rem;">
        <h2>Actions</h2>

        <form method="post"
              onsubmit="return confirm('ATTENTION !!! Cette action est irréversible.\nConfirmer la suppression de votre compte ?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
            <button type="submit" name="delete_account" class="btn btn-danger">
                Supprimer mon compte
            </button>
        </form>
    </section>

    <p style="margin-top:1rem;">
        <a href="<?= htmlspecialchars(APP_URL) ?>/public/index.php">← Retour à l’accueil</a>
    </p>
</main>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>