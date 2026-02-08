<?php
declare(strict_types=1);
// Gestion des sessions
require_once 'config.php';



// Démarrer une session sécurisée si aucune en cours
function startSecureSession(): void {                   // !!!! Pourquoi tu veux supprimer le fait que c'est une fonction ?
    if (session_status() === PHP_SESSION_NONE) {
        session_start([                                 // !!! Pour le coup, il me semble que httponly on en a parlé en cours
            'cookie_httponly' => true,
            'cookie_secure' => !APP_DEBUG, // permet d'avoir false (pour site en http) si APP_DEBUG est en true (dev)
            'cookie_samesite' => 'Strict',
        ]);
    }
}

// vérifier si un utilisateur est connecté (ATTENTION, ce n'est pas la même chose que d'avoir une session)
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);         // !!! user ou user_id ?
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
    session_regenerate_id(true);                    // !!! tu n'as gardé que le  ['user']= $username})
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
}

// Déconnexion
function logout(): void {               //https://laconsole.dev/formations/php/sessions#:~:text=Pour%20supprimer%20une%20variable%20de,copier&text=Il%20est%20%C3%A9galement%20possible%20de,la%20variable%20superglobale%20%24_SESSION%20.
    $_SESSION = array();                 // vide la mémoire 
    //session_unset();                // pour vider une variable de session spécifique
    session_destroy();              // détruit la session
    header('Location: ' .APP_URL.'/public/index.php');  // redirige vers index après déconnexion
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