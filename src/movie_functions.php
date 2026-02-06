<?php

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

function getMoviesByCategory($name)
{
    global $pdo;

    $sql = "
        SELECT m.id, m.title, m.price, m.poster_url
        FROM movies m
        JOIN movie_category mc ON m.id = mc.movie_id
        JOIN categories c ON mc.category_id = c.id
        WHERE c.name = :name
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMovieById($id)
{
    global $pdo;

    $sql = "
        SELECT *
        FROM movies
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getMoviesByDirector($directorId)
{
    global $pdo;

    $sql = "
        SELECT id, title, price, poster_url
        FROM movies
        WHERE director_id = :director_id
    ";

    $stmt = $pdo->prepare($sql); // Protège de l'injection SQL
    $stmt->bindValue(':director_id', (int)$directorId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}