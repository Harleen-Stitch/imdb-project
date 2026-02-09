<?php 
declare(strict_types=1);
// public/movie.php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/src/movie_functions.php';
require_once dirname(__DIR__) . '/src/category_functions.php';
require_once dirname(__DIR__) . '/src/cart_functions.php';
require_once dirname(__DIR__) . '/includes/security.php';
startSecureSession();
getCurrentUser();

// Récupération de l'id du film via GET
$movieId = $_GET['id'] ?? '';

$backUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php';

$movie = null;
$categories = [];
$actors = [];

// Si un id est fourni, on récupère le film
if (!empty($movieId)) {
    $movie = getMovieById($movieId);
    $categories = getCategoriesByMovie($movieId);
    $actors = getActorsByMovie($movieId);
}

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <?php if (empty($movieId)) : ?>
        <p>Aucun film sélectionné.</p>

    <?php elseif (!$movie) : ?>
        <p>Film introuvable.</p>

    <?php else : ?>
        <article class="movie-detail">
            <h1><?= htmlspecialchars($movie['title']) ?></h1>

            <?php if (!empty($movie['poster_url'])) : ?>
                <img
                    src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['poster_url'])?>"
                    alt="<?= htmlspecialchars($movie['title']) ?>"
                >
            <?php endif; ?>

            <p><strong>Prix :</strong> <?= htmlspecialchars($movie['price']) ?> €</p>

            <!-- Résumé -->
            <?php if (!empty($movie['description'])) : ?>
                <p>
                    <strong>Résumé :</strong><br>
                    <?= htmlspecialchars($movie['description']) ?>
                </p>
            <?php endif; ?>

            <!-- Catégories -->
            <p>
                <strong>Catégories :</strong>
                <?php if (empty($categories)) : ?>
                <?php else : ?>
                    <?php foreach ($categories as $category) : ?>
                        <a href="category.php?name=<?= urlencode($category['name']) ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </p>

            <!-- Réalisateur -->
            <?php if (!empty($movie['director_id'])) : ?>
                <p>
                    <strong>Réalisateur :</strong>
                        <a href="director.php?id=<?= (int) $movie['director_id'] ?>">
                        <?= htmlspecialchars($movie['director_name']) ?>
                    </a>
                </p>
            <?php endif; ?>

            <!-- Acteurs -->
            <p>
                <strong>Acteurs :</strong>
                <?php if (empty($actors)) : ?>
                <?php else : ?>
                    <?php foreach ($actors as $actor) : ?>
                        <span><?= htmlspecialchars($actor['name']) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </p>

            <!-- Panier -->
            <p>
                <form method="post" action="cart.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                    <button type="submit" name="add_to_cart">Ajouter au panier</button>
                </form>
            </p>

            <!-- Retour -->
            <p>
                <a href="<?= htmlspecialchars($backUrl) ?>">Retour à la liste</a>
            </p>
        </article>
    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>