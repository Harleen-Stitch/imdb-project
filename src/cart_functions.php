<?php
// src/cart.php
function getUserIdByUsername($username)
{
    global $pdo;

    $sql = "SELECT id FROM users WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $id = $stmt->fetchColumn();
    return $id ? (int) $id : null;
}

function addToCart($userId, $movieId)
{
    global $pdo;

    $sql = "
        INSERT IGNORE INTO cart_items (user_id, movie_id)
        VALUES (:user_id, :movie_id)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
    $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
    $stmt->execute();
}

function removeFromCart($userId, $movieId)
{
    global $pdo;

    $sql = "
        DELETE FROM cart_items
        WHERE user_id = :user_id AND movie_id = :movie_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
    $stmt->bindValue(':movie_id', (int) $movieId, PDO::PARAM_INT);
    $stmt->execute();
}

function emptyCart($userId)
{
    global $pdo;

    $sql = "DELETE FROM cart_items WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
    $stmt->execute();
}

function getCartMovies($userId)
{
    global $pdo;

    $sql = "
        SELECT m.id, m.title, m.price, m.poster_url
        FROM cart_items ci
        JOIN movies m ON m.id = ci.movie_id
        WHERE ci.user_id = :user_id
        ORDER BY m.title ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrdersByUser($userId)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT id, total_amount, status, created_at
        FROM orders
        WHERE user_id = :user_id
        ORDER BY created_at DESC
    ");
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderItems($orderId)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT m.title, oi.price
        FROM order_items oi
        JOIN movies m ON m.id = oi.movie_id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute(['order_id' => $orderId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function checkout(int $userId) {
    global $pdo;
    $cartItems = getCartMovies($userId);
    if (empty($cartItems)) {
        throw new RuntimeException("Panier vide");
    }
    $cartTotal = 0;
    foreach ($cartItems as $movie) {
        $cartTotal += (float) $movie['price'];
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at)
        VALUES (:user_id, :total_amount, :status, NOW())");
        $stmt->execute([
        ':user_id' => $userId,
        ':total_amount' => $cartTotal,
        ':status' => 'paid']);

        $orderId = (int)$pdo->lastInsertId();
        $oi = $pdo->prepare("
        INSERT INTO order_items (order_id, movie_id, price)
        VALUES (:order_id, :movie_id, :price)");

        foreach ($cartItems as $movie) {
            $oi->execute([
                ':order_id' => $orderId,
                ':movie_id' => (int)$movie['id'],
                ':price' => (float)$movie['price'],
            ]);
        } // vider le panier

        $ec = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id");
        $ec->execute([':user_id'=> $userId]);
        $pdo->commit();
        return $orderId;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
