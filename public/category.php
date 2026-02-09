<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php';   // .env
require_once dirname(__DIR__) . '/includes/db.php';       // PDO
require_once dirname(__DIR__) . '/includes/auth.php';     // Auth
require_once dirname(__DIR__) . '/src/category_functions.php'; // Fonctions films
require_once dirname(__DIR__) . '/includes/security.php';

startSecureSession();
getCurrentUser();

// Récupération du nom de la catégorie
$categoryName = $_GET['name'] ?? '';

// Harmoniser français dans la BDD et anglais dans l'URL
$mapEnToFr = [
    'Action' => 'Action',
    'Adventure' => 'Aventure',
    'Animation' => 'Animation',
    'Comedy' => 'Comédie',
    'Crime' => 'Crime',
    'Documentary' => 'Documentaire',
    'Drama' => 'Drame',
    'Family' => 'Familial',
    'Fantasy' => 'Fantastique',
    'History' => 'Histoire',
    'Horror' => 'Horreur',
    'Music' => 'Musique',
    'Mystery' => 'Mystère',
    'Romance' => 'Romance',
    'Science Fiction' => 'Science-fiction',
    'TV Movie' => 'Téléfilm',
    'Thriller' => 'Thriller',
    'War' => 'Guerre',
    'Western' => 'Western'
];

// Si la catégorie est en anglais, on la convertit en français
$categoryNameFr = $mapEnToFr[$categoryName] ?? $categoryName;

// Récupération des films de la catégorie
$movies = getMoviesByCategory($categoryNameFr);

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Catégorie : <?= htmlspecialchars($categoryNameFr) ?></h1> <!-- htmlspecialchars pour empêcher les injections -->
    
    <section class="movies-list">
        <?php if (empty($movies)) : ?>
            <p>Aucun film trouvé pour cette catégorie.</p>
        <?php else : ?>
            <?php foreach ($movies as $movie) : ?>
                <?php include dirname(__DIR__) . '/includes/movie_card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

<?php

include dirname(__DIR__) . '/includes/footer.php';
?>