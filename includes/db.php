<?php
/** Force le typage strict en PHP
 * Evite les conversions automatiques dangereuses
 * Améliore la sécurité et la fiabilité
 * best practice moderne
 */
declare(strict_types=1);

if (!defined('DB_HOST')) {
    die('Erreur : config.php doit être chargé avant db.php');
}

try {
    // Options de sécurité POD
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,            // Lever des exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Tableau associatif par défaut, pour faciliter la lecture
        PDO::ATTR_EMULATE_PREPARES => false,                    // Vraies requêtes préparées
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"     // Encodage UTF-8
    ];

    // Connexion PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST ."; port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        $options
    );
} catch (PDOException $exception) {
    // Gestion différente selon l'environnement dev ou prod
    if (APP_DEBUG) { # Donc si env de dev
        die('Erreur de connection à la BDD : ' . $exception->getMessage());
    } else {
        // donc en prod
        error_log('Erreur BDD : ' . $exception->getMessage());      // Message dans les logs
        die('Une erreur est survenue. Veuillez réessayer ultérieurement.'); // Message visible par le visiteur
    }
}

?>