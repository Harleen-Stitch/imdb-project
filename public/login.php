<?php
#######################
## PAGE DE CONNEXION ##
#######################

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php'; # appelle session_start()
require_once dirname(__DIR__) . '/includes/security.php';

startSecureSession();

if (isLoggedIn()) {
    include dirname(__DIR__) . '/includes/header.php';
    ?>
    <main>
        <p>
            Vous êtes déjà connecté ! <br> 
            Que voulez-vous faire ?
        </p>
        <div>
            <a href="index.php" class="btn">Allez à l'accueil</a>
            <a href="logout.php" class="btn">Vous déconnecter</a>
        </div>
    </main>
    <?php 
    include dirname(__DIR__) . '/includes/footer.php';
    exit;
}

/* donc si pas connecté, on affichera le formulaire.
Pour éviter les erreurs de chargement, on met d'abord la requête puis le formulaire */
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Validation des données
    if(!verifyCsrfToken($csrf_token)) {
        $error = "Une erreur est survenue"; 
        error_log("Tentative CSRF détectée sur login.php"); // Idéalement, il faudrait envoyer une erreur au log plus détaillée
    } elseif (empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires"; 
    } elseif (!validateEmail($email)) {
        $error = "Format email invalide." ;
    } else {
        try {
            // chercher l'email de l'utilisateur
            $check = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
            $check->execute([$email]);
            $user = $check->fetch(PDO::FETCH_ASSOC);

            // Vérifier le couple email/mdp
            if ($user && verifyPassword($password, $user['password_hash'])) {
                login($user['id'], $user['username'], $user['email']);
                header('Location: ' .APP_URL.'/public/index.php');
                exit;
            }
            $error = "Identifiant et/ou mot de passe incorrect.";
            usleep(500000); // anti brut force : blocage de 500 ms => source : https://www.php.net/manual/fr/function.usleep.php
        } catch (PDOException $exception) {
            $error = APP_DEBUG ? "Erreur BDD: " . $exception->getMessage(): "Une erreur est survenue. Veuillez réessayer ultérieurement.";
            error_log("PODException register.php: " . $exception->getMessage());
        }
    }


}
include dirname(__DIR__) . '/includes/header.php';
?>
    <main>
        <div>
            <h1>Connexion</h1>
            <form method="POST" action="login.php">
                <!-- Token CSRF caché -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                 <!-- CHAMPS EMAIL -->
                <div class="champs-form">
                    <label for="email">E-mail</label>
                    <input  type="email"
                            id="email"
                            name="email"
                            placeholder="exemple@exemple.fr"
                            autocomplete="email"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                 <!-- CHAMPS MOT DE PASSE -->
                <div class="champs-form">
                    <label for="password">Mot de passe</label>
                    <input  type="password"
                            id="password"
                            name="password"
                            placeholder="l50(wSj8TSf%q!sf^"
                            autocomplete="current-password"
                            required
                    >
                </div>
                <!-- BOUTON D ENVOI -->
                 <button type="submit">Se connecter</button>
            </form>

            <!-- SI PAS ENCORE INSCRIT -->
             <p>Pas encore inscrit ? <a href="register.php">S'inscrire</a></p>
        </div>
    </main>

<?php
// Footer HTML
include dirname(__DIR__) . '/includes/footer.php';

?>