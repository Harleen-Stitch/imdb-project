
<?php 
// public/index.php
// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD

include dirname(__DIR__) . '/includes/header.php'; # header html
?>

<main>
    <h1 style='color: green'>Hello World!</h1>
    <p>Bienvenue sur IMDB Project</p>
    
    

</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>