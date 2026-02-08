<?php
################################
## PAGE DE CREATION DE COMPTE ##
################################

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
            <p>Ce n'est pas votre compte : <a href="logout.php" class="btn">Vous déconnecter</a></p>;
        </div>
    </main>

    <?php 
    include dirname(__DIR__) . '/includes/footer.php';
    exit;
}
    
    $error = null;
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_conf = $_POST['password_conf'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';

    // Validation des données
    if(!verifyCsrfToken($csrf_token)) {
        $error = "Une erreur est survenue"; 
        error_log("Tentative CSRF détectée sur register.php"); // Idéalement, il faudrait envoyer une erreur au log plus détaillée
    } elseif (empty($username) || empty($email) || empty($password) || empty($password_conf)) {
        $error = "Tous les champs sont obligatoires"; 
    } elseif (!validateEmail($email)) {
        $error = "Format email invalide." ;
    } elseif (strlen($password) <14) {
        $error = "Mot de passe trop court : 14 caractères minimum";
    } elseif ($password !== $password_conf) {
        $error = "Les mots de passe ne sont pas identiques.";
    }

    if ($error === null) {
        try { // vérifie si l'email est déjà dans la BDD
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            $emailExist = $check->fetch();

            if ($emailExist) {
                $error = "Cette adresse e-mail existe déjà. <a href=\"login.php\">Se connecter</a>";
            } else {
            // On a validé les données. On continue
            // On hash le mot de passe
                $passwordHash = hashPassword($password);

                    // On insert l'utilisateur dans la BDD
                $insert = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES( ?, ?, ?)");
                $insert->execute([$username, $email, $passwordHash]);

                // redirection si validation
                header('Location: login.php?register=1');
                exit;
            }
        } catch (PDOException $exception) {
            $error = APP_DEBUG ? "Erreur BDD: " . $exception->getMessage(): "Une erreur est survenue. Veuillez réessayer ultérieurement."; 
            error_log("PDOException register.php: " . $exception->getMessage());
        }
    }
}

include dirname(__DIR__) . '/includes/header.php';
?>
    <main>
        <div>
            <h1>Inscription</h1>
            <div>
                <?php if (!empty($error)): ?>
                    <div>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="form">
                    <!-- Token CSRF caché -->
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <!-- CHAMPS EMAIL -->
                    <div class="champs-form">
                        <label for="username">Nom d'utilisateur :</label>
                        <input  type="text"
                                id="username"
                                name="username"
                                placeholder="John_Doe"
                                autocomplete="username"
                                required
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        >
                    </div>
                    <div class="champs-form">
                        <label for="email">E-mail :</label>
                        <input  type="email"
                                id="email"
                                name="email"
                                placeholder="exemple@exemple.fr"
                                autocomplete="email"
                                required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >
                    </div>

                    <!-- CHAMPS MOT DE PASSE -->
                    <div class="champs-form">
                        <label for="password">Mot de passe :</label>
                        <input  type="password"
                                id="password"
                                name="password"
                                placeholder="Minimum 14 caractères requis"
                                autocomplete="new-password"
                                required
                                minlength="14"
                        >
                    </div>

                    <!-- CONFIRMATION MOT DE PASSE -->
                    <div class="champs-form">
                        <label for="password_conf">Confirmer mot de passe :</label>
                        <input  type="password"
                                id="password_conf"
                                name="password_conf"
                                placeholder="Retapez votre mot de passe à l'identique"
                                required
                                minlength="14"
                        >
                    </div>
                    <!-- BOUTON D ENVOI -->
                    <button type="submit">S'inscrire</button>
                </form>
            </div>
        </div>
    </main>

<?php
// Footer HTML
include dirname(__DIR__) . '/includes/footer.php';

?>