<?php 

# require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/src/director_functions.php';
getCurrentUser();


// Récupération de l'id du réalisateur via GET
$directorId = $_GET['id'] ?? null;

if (!$directorId) {
    die('Réalisateur invalide');
}

$director = getDirectorById($directorId);
$movies = getMoviesByDirector($directorId);

$movies = [];

// Si un id est fourni, on récupère les films du réalisateur
if (!empty($directorId)) {
    $movies = getMoviesByDirector($directorId);
}

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Films de <?= htmlspecialchars($director['name']) ?></h1>

    <?php if (empty($directorId)) : ?>
        <p>Veuillez choisir un réalisateur.</p>

    <?php elseif (empty($movies)) : ?>
        <p>Aucun film trouvé pour ce réalisateur.</p>

    <?php else : ?>
        <section class="movies-list">
            <?php if (empty($movies)) : ?>
                <p>Aucun film trouvé pour cette catégorie.</p>
            <?php else : ?>
                <?php foreach ($movies as $movie) : ?>
                    <?php include dirname(__DIR__) . '/includes/movie_card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>