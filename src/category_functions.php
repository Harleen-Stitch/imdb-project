<?php

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

function getCategoriesByMovie($movieId)
{
    global $pdo;

    $sql = "
        SELECT c.id, c.name
        FROM categories c
        JOIN movie_category mc ON c.id = mc.category_id
        WHERE mc.movie_id = :movie_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}