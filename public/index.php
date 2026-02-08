<?php 
// public/index.php
// permet d'appeler les classes et sera valable pour tous les fichiers
require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/db.php'; # BDD
require_once dirname(__DIR__) . '/includes/auth.php';
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

<main>
    <h1 style="color: green;">Hello World!</h1>
    <p>Bienvenue sur IMDB Project</p>

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