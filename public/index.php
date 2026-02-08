<?php 
// public/index.php
// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/security.php';
require_once dirname(__DIR__) . '/src/movie_functions.php';
startSecureSession();
getCurrentUser();

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

// Récupération des films
$movies = getMoviesPaginated($limit, $offset);

// Header
include dirname(__DIR__) . '/includes/header.php';
?>

    <main> <!-- affiche "à vous" ou le nom de l'utilisateur -->
        <!-- https://laconsole.dev/formations/php/sessions#:~:text=Pour%20supprimer%20une%20variable%20de,copier&text=Il%20est%20%C3%A9galement%20possible%20de,la%20variable%20superglobale%20%24_SESSION%20. -->
        <h1 style="color: green;">Bievenue <?php
        if (!empty($_SESSION['username'])) {
        echo $_SESSION['username'];
        } else {
        echo "à vous";
        } ?>
    sur IMDB Projet, <br> LE site de référence pour vos achats de films !</h1>
        <p>Inutile de chercher plus longtemps ! Avec IMDB Projet, retrouver facilement tous vos films préférés et achetez-les en quelques clics !</p>
        <p> Vous ne savez pas par où commencer ? Voici une première sélection :</p>

        <section class="movies-list">
            <?php if (empty($movies)) : ?>
                <p>Aucun film trouvé pour cette catégorie.</p>
            <?php else : ?>
                <?php foreach ($movies as $movie) : ?>
                    <?php include dirname(__DIR__) . '/includes/movie_card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>


        <nav class="pagination">
            <?php if ($page > 1) : ?>
                <a href="index.php?page=<?= $page - 1 ?>">Page précédente</a>
            <?php endif; ?>

            <a href="index.php?page=<?= $page + 1 ?>">Page suivante</a>
        </nav>
    </main>

<?php
// Footer HTML
include dirname(__DIR__) . '/includes/footer.php';

?>