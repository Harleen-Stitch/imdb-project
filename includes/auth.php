<?php
// Gestion des sessions
require_once 'config.php';

declare(strict_types=1);

// Démarrer une session sécurisée si aucune en cours
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => !APP_DEBUG, // permet d'avoir false (pour site en http) si APP_DEBUG est en true (dev)
            'cookie_samesite' => 'Strict',
        ]);
    }
}

// vérifier si un utilisateur est connecté (ATTENTION, ce n'est pas la même chose que d'avoir une session)
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}


// Redirection si non connecté
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' .APP_URL.'/public/login.php');
        exit();
    }
}

// Connexion
function login(int $user_id, string $username, string $email):void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
}

// Déconnexion
function logout(): void {
    $_SESSION = [];                 // vide la mémoire
    session_destroy();              // détruit la session
    header('Location : ' .APP_URL.'/public/index.php');  // redirige vers index après déconnexion
    exit();
}

// Récupérer l'utilisateur connecté
function getCurrentUser(): array { 
    return [
        // retourne une valeur si user connecté, sinon, valeur null et nom générique
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? 'invité',
        'email' => $_SESSION['email'] ?? null
    ];
}
?>