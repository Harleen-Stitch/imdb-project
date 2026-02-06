<?php

$API_KEY = 'eafcbb9e08ccc5953d55168e8888feb9'; // A cacher plus tard si on laisse ce script ici
$LANG = 'fr-FR';
$MOVIES_PER_CATEGORY = 20;

$sql = [];

// Eviter les problèmes avec les apostrophes et caractères spéciaux
function esc($value)
{
    return addslashes($value);
}

// Catégories

// Récupère la liste complète des genres (id et nom) en json
$genresJson = file_get_contents("https://api.themoviedb.org/3/genre/movie/list?api_key=$API_KEY&language=$LANG");
// Transforme le json en tableau
$genres = json_decode($genresJson, true);

foreach ($genres['genres'] as $genre) {
    // Insère la catégorie dans la table
    $sql[] = "INSERT INTO categories (name)
              VALUES ('" . esc($genre['name']) . "');";
}

// Films
foreach ($genres['genres'] as $genre) {

    $page = 1;
    $count = 0;

    while ($count < $MOVIES_PER_CATEGORY) {

        $moviesJson = file_get_contents(
            "https://api.themoviedb.org/3/discover/movie?api_key=$API_KEY&language=$LANG&with_genres={$genre['id']}&page=$page"
        );
        $movies = json_decode($moviesJson, true);

        foreach ($movies['results'] as $movie) {

            if ($count >= $MOVIES_PER_CATEGORY) {
                break;
            }

            // attribue un prix selon l'année
            $year = (int) substr($movie['release_date'], 0, 4); // extrait les 4 premiers chiffres donc l'année

            if ($year <= 1980) {
                $price = 9.99;
            } elseif ($year <= 2010) {
                $price = 14.99;
            } else {
                $price = 19.99;
            }

            // Récupère la durée du film
            $detailsJson = file_get_contents(
                "https://api.themoviedb.org/3/movie/{$movie['id']}?api_key=$API_KEY&language=$LANG"
            );
            $details = json_decode($detailsJson, true);
            $duration = $details['runtime'] ?? null;

            // Insère le film dans la table
            $sql[] = "INSERT INTO movies (title, description, price, release_year, tmdb_id, poster_url, duration)
                      VALUES (
                        '" . esc($movie['title']) . "',
                        '" . esc($movie['overview']) . "',
                        $price,
                        $year,
                        {$movie['id']},
                        '" . esc($movie['poster_path']) . "',
                        " . ($duration !== null ? $duration : "NULL") . "
                      );";

            // Crée le lien entre le film et la catégorie
            $sql[] = "INSERT INTO movie_category (movie_id, category_id)
                      VALUES (
                        (SELECT id FROM movies WHERE tmdb_id = {$movie['id']} LIMIT 1),
                        (SELECT id FROM categories WHERE name = '" . esc($genre['name']) . "' LIMIT 1)
                      );";

            // Réalisateur et acteurs
            $creditsJson = file_get_contents(
                "https://api.themoviedb.org/3/movie/{$movie['id']}/credits?api_key=$API_KEY"
            );
            $credits = json_decode($creditsJson, true);

            foreach ($credits['crew'] as $crew) {

                if ($crew['job'] === 'Director') {

                    // Insère le réalisateur (sans doublon)
                    $sql[] = "INSERT IGNORE INTO directors (name)
                              VALUES ('" . esc($crew['name']) . "');";

                    // Crée le lien entre le réalisateur et le film
                    $sql[] = "UPDATE movies
                              SET director_id = (
                                  SELECT id FROM directors WHERE name = '" . esc($crew['name']) . "' LIMIT 1
                              )
                              WHERE tmdb_id = {$movie['id']};";
                }
            }

            foreach ($credits['cast'] as $actor) {

                // Insère l'acteur (sans doublon)
                $sql[] = "INSERT IGNORE INTO actors (name)
                          VALUES ('" . esc($actor['name']) . "');";

                // Crée le lien entre l'acteur et le film
                $sql[] = "INSERT IGNORE INTO movie_actor (movie_id, actor_id, role)
                          VALUES (
                            (SELECT id FROM movies WHERE tmdb_id = {$movie['id']} LIMIT 1),
                            (SELECT id FROM actors WHERE name = '" . esc($actor['name']) . "' LIMIT 1),
                            '" . esc($actor['character']) . "'
                          );";
            }

            $count++;
        }

        $page++;
    }
}

// Création de seed.sql
file_put_contents(__DIR__ . '/../database/seed.sql', implode("\n", $sql)); // Ecrit tout le tableau sous forme de lignes
echo "seed.sql généré";