<?php

// index.php tous les films
function getAllMovies($limit = 10)
{
    global $pdo;

    $sql = "
        SELECT id, title, price, poster_url
        FROM movies
        ORDER BY created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMoviesPaginated($limit, $offset)
{
    global $pdo;

    $sql = "
        SELECT id, title, price, poster_url
        FROM movies
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMovieById($id)
{
    global $pdo;

    $sql = "
        SELECT
            m.*,
            d.id AS director_id,
            d.name AS director_name
        FROM movies m
        JOIN directors d ON m.director_id = d.id
        WHERE m.id = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getActorsByMovie($movieId)
{
    global $pdo;

    $sql = "
        SELECT a.id, a.name
        FROM actors a
        JOIN movie_actor ma ON a.id = ma.actor_id
        WHERE ma.movie_id = :movie_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchMovies($query)
{
    global $pdo;

    $sql = "
        SELECT DISTINCT m.id, m.title, m.price, m.poster_url
        FROM movies m
        LEFT JOIN directors d ON m.director_id = d.id
        WHERE m.title LIKE :query
           OR d.name LIKE :query
        ORDER BY m.title ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}