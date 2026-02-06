<?php

require_once dirname(__DIR__) . '/includes/config.php';   // .env
require_once dirname(__DIR__) . '/includes/db.php';       // PDO
require_once dirname(__DIR__) . '/includes/auth.php';     // Auth
require_once dirname(__DIR__) . '/src/movie_functions.php'; // Fonctions films

getCurrentUser();

// Récupération du nom de la catégorie
$categoryName = $_GET['name'] ?? '';

// Récupération des films de la catégorie
$movies = getMoviesByCategory($categoryName);

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Catégorie : <?= htmlspecialchars($categoryName) ?></h1> <!-- htmlspecialchars pour empêcher les injections -->
    
    <section class="movies-list">
        <?php if (empty($movies)) : ?>
            <p>Aucun film trouvé pour cette catégorie.</p>
        <?php else : ?>
            <?php foreach ($movies as $movie) : ?>
                <article class="movie-card">
                    <!-- Nom du film-->
                    <h2><?= htmlspecialchars($movie['title']) ?></h2>

                     <!-- Affiche si elle existe -->
                    <?php if (!empty($movie['poster_url'])) : ?>
                        <img
                            src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['poster_url']) ?>"
                            alt="<?= htmlspecialchars($movie['title']) ?>"
                        >
                    <?php endif; ?>

                     <!-- Prix-->
                    <p>Prix : <?= htmlspecialchars($movie['price']) ?> €</p>

                    <!-- Lien vers movie-->
                    <a href="movie.php?id=<?= (int) $movie['id'] ?>">
                        Voir
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php

include dirname(__DIR__) . '/includes/footer.php';
?>