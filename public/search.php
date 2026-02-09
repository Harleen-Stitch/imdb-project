<?php 
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/src/movie_functions.php';
require_once dirname(__DIR__) . '/includes/security.php';

startSecureSession();
getCurrentUser();

$query = $_GET['q'] ?? '';
$movies = [];

if (!empty($query)) {
    $movies = searchMovies($query);
}

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Recherche</h1>

    <form method="get" action="search.php">
        <input
            type="text"
            name="q"
            placeholder="Rechercher un film ou un réalisateur"
            value="<?= htmlspecialchars($query) ?>"
        >
        <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($query)) : ?>
        <h2>Résultats pour « <?= htmlspecialchars($query) ?> »</h2>

        <section class="movies-list">
            <?php if (empty($movies)) : ?>
                <p>Aucun résultat trouvé.</p>
            <?php else : ?>
                <?php foreach ($movies as $movie) : ?>
                    <?php include dirname(__DIR__) . '/includes/movie_card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>