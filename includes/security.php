<?php
// includes/security.php
##########################################
##  FONCTIONS DE SECURITE ET VALIDATION ##
##########################################

declare(strict_types=1);
function sanitizeInput(string $data): string {
    $data = trim($data);                                    // prend les données entrantes et retire les espaces
    $data = strip_tags($data);                            // prend les données nettoyées et retire les balises
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');   // prend les données renettoyées et échappe les guillemets, <, >, &
    return $data;                                           // les données sont nettoyées
}

// vérifie que les entrées emails sont valides
function validateEmail(string $email): bool {
    $email = trim($email);                              // supprime les espaces avant les emails
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// génération de token
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token']))
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));         // 64 caractères hexa
    return $_SESSION['csrf_token'];
}

// protection contre les jetons CSRF
function verifyCsrfToken(string $token):bool {
    // vérifie qu'une session est active, sinon pas de token
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    
    $sessionToken = $_SESSION['csrf_token'] ?? null;  // prend le csrf_token s'il y en a un ET n'est pas nul
    if (!is_string($sessionToken) || !is_string($token) || $sessionToken === '' || $token === ''){    
        return false;
    }

    ## UNIQUEMENT SI TOKEN EN BIN2HEX A RETIRER SINON
    // défense en profondeur uniquement si on génère le token en bin2hex(random_bytes(32))
    // protège contre les tokens malformés ou manipulés
    $isHex64 = (bool)preg_match('/^[a-f0-9]{64}$/i', $token) && (bool)preg_match('/^[a-f0-9]{64}$/i', $sessionToken) ;
    if (!$isHex64) {
       return false;
    }

    $ok = hash_equals($sessionToken, $token);
    
    if ($ok) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // génère un nouveau token pour la prochaine fois
        }
    return $ok;
}

// hash mot de passe
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);       // 12 = standard de lenteur sur serveur standard
}

// vérifier si le mot de passe est le bon
function verifyPassword (string $password, string $hash): bool {
    return password_verify($password, $hash);
}
?>