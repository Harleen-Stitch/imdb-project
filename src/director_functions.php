<?php

function getMoviesByDirector($directorId)
{
    global $pdo;

    $sql = "
        SELECT DISTINCT m.id, m.title, m.price, m.poster_url
        FROM movies m
        WHERE m.director_id = :director_id
        ORDER BY m.id DESC
    ";

    $stmt = $pdo->prepare($sql); // Protège de l'injection SQL
    $stmt->bindValue(':director_id', (int)$directorId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDirectorById($id)
{
    global $pdo;

    $sql = "
        SELECT id, name
        FROM directors
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}