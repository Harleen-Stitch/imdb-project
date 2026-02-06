<?php
// public/index.php
// Page d’accueil – affichage des films (lecture seule)

# require_once dirname(__DIR__) . '/vendor/autoload.php'; // Composer (non utilisé)
require_once dirname(__DIR__) . '/includes/config.php';   // .env
require_once dirname(__DIR__) . '/includes/db.php';       // PDO
require_once dirname(__DIR__) . '/includes/auth.php';     // Auth
require_once dirname(__DIR__) . '/src/movie_functions.php'; // Fonctions films

getCurrentUser();

// Récupération des films
$movies = getAllMovies(10);

// Header HTML
include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1 style="color: green;">Hello World!</h1>
    <p>Bienvenue sur IMDB Project</p>

    <section class="movies-list">
        <?php if (empty($movies)) : ?>
            <p>Aucun film disponible.</p>
        <?php else : ?>
            <?php foreach ($movies as $movie) : ?>
                <article class="movie-card">
                    <h2><?= htmlspecialchars($movie['title']) ?></h2>

                    <?php if (!empty($movie['poster_url'])) : ?>
                        <img
                            src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['poster_url']) ?>"
                            alt="<?= htmlspecialchars($movie['title']) ?>"
                        >
                    <?php endif; ?>

                    <p>Prix : <?= htmlspecialchars($movie['price']) ?> €</p>

                    <a href="movie.php?id=<?= (int) $movie['id'] ?>">
                        Voir
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php
// Footer HTML
include dirname(__DIR__) . '/includes/footer.php';
?>