<?php
#########################
## PAGE DE DECONNEXION ##
#########################

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php'; # appelle session_start()
require_once dirname(__DIR__) . '/includes/security.php';

startSecureSession();

if (!isLoggedIn()) {
    include dirname(__DIR__) . '/includes/header.php';
    ?>
    <main>
        <p>
            Vous n'êtes pas connecté ! <br> 
            Que voulez-vous faire ?
        </p>
        <div>
            <a href="index.php" class="btn">Allez à l'accueil</a>
            <a href="login.php" class="btn">Se connecter</a>
        </div>
    </main>
    <?php 
    include dirname(__DIR__) . '/includes/footer.php';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf_token)) {
        error_log('Erreur de CSRF détecté sur logout.php');
        header('Location: ' . APP_URL . '/public/index.php');
        exit;
    } else {
        logout(); ?>
        <p>"Vous êtes bien déconnecté"
            <br>
            <a href="<?= APP_URL ?>/public/lindex.php">Retourner à l'accueil</a>
        </p>
        <?php 
        exit;
    }

    
    exit;
?>