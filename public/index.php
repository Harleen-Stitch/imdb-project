<?php 
// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD

include dirname(__DIR__) . '/includes/header.php'; # header html

    echo "Hello World!"
?>

<main>
    <h1>Hello World!</h1>
    <p>Bienvenue sur IMDB Project</p>
    
    <?php
    // Test de connexion BDD
    try {
        $stmt = $pdo->query("SELECT 1");
        echo "<p style='color: green;'>✅ Connexion BDD réussie !</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Erreur de connexion BDD</p>";
    }
    ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>