
<?php 
// public/index.php
// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php';
startSecureSession();
getCurrentUser();

include dirname(__DIR__) . '/includes/header.php'; # header html
?>

<main>
    <h1>IMDB Project</h1>
    
    <?php
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM movies");
        $result = $stmt->fetch();
        echo "<p style='color: green;'>✅ Connexion BDD réussie !</p>";
        echo "<p>Nombre de films en base : <strong>" . $result['total'] . "</strong></p>";
        
        // Afficher les films
        $stmt = $pdo->query("SELECT title, price FROM movies LIMIT 5");
        echo "<ul>";
        while ($movie = $stmt->fetch()) {
            echo "<li>{$movie['title']} - {$movie['price']}€</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Erreur BDD : " . $e->getMessage() . "</p>";
    }
    ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>