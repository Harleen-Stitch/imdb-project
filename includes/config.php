<?php
#########################################
##  FICHIER DE CONFIGURATION DU PROJET ##
#########################################
/* si simplification a faire, prendre la version de Marie :
    define('DB_HOST', 'localhost');
define('DB_NAME', 'imdb_project');
define('DB_USER', 'root');
*/


/** Force le typage strict en PHP
 * Evite les conversions automatiques dangereuses
 * Améliore la sécurité et la fiabilité
 * best practice moderne
 */
declare(strict_types=1);

// Charge les variables d'environnement
use Dotenv\Dotenv;

/** Charger .env si pas déjà fait. Le fichier définit la configuration de base. Ici, on vérifie si les variable d'env sont déjà chargé, sinon, on les charge. Evite les doublons. A priori, il y a deux approches :
 * - explicite => c'est chargé dnas index donc pas besoin de charger ailleurs
 * - défensive distribuée: on recharge dans les fichiers importants, mais cela risque de créer des doublons
*/ 

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv -> load();

// Force la présence des varaibles
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER']);

//Valide que DB_PORT est un nombre
$dotenv->required('DB_PORT')->isInteger();

/** Constante de configuration
 * Ici, on les définit, même si on va les chercher dans .env
 * Cela permet de créer des constantes donc évite les modifications malencontreuses
 * 
 * On met les valeurs dans :
 * .env = elles sont hors du codes
 * $_ENV = PHP peut les lire
 * APP_* = centralisée, typée, simples, immuables et propres à utiliser
 * 
 */
// Constantes de config
defined('APP_ENV')      || define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
defined('APP_DEBUG')    || define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN));
defined('APP_URL')      || define('APP_URL', rtrim($_ENV['APP_URL'] ?? '', '/'));

// Constantes de chemins
defined('ROOT_PATH')    || define('ROOT_PATH', dirname(__DIR__));
defined('PUBLIC_PATH')  || define('PUBLIC_PATH', ROOT_PATH . '/public');
defined('UPLOAD_PATH')  || define('UPLOAD_PATH', PUBLIC_PATH . '/assets/images/movies');

// COnstantes de BDD
// Pas de fallback par mesure de sécurité
defined('DB_HOST')      || define('DB_HOST', $_ENV['DB_HOST']);
defined('DB_NAME')      || define('DB_NAME', $_ENV['DB_NAME']);
defined('DB_USER')      || define('DB_USER', $_ENV['DB_USER']);
defined('DB_PASS')      || define('DB_PASS', $_ENV['DB_PASS']);
defined('DB_PORT')      || define('DB_PORT', (int)$_ENV['DB_PORT']);

// Constante de sécurité
defined('SESSION_LIFETIME') || define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? '7200'));

// Configuration PHP selon l'environnement
if (APP_DEBUG){
    // Donc en dev <=> on affiche les erreurs
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // donc en prod <=> on n'affiche surtout pas les erreurs
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Header de sécurité (avant tout output HTML)
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Content Security Policy (protection XSS avancé)
if (!APP_DEBUG) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; img-src 'self' data:;");
}

// Timezone
date_default_timezone_set('Europe/Paris');

