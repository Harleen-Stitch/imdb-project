<?php

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/src/movie_functions.php';

requireLogin();

$username = getCurrentUser();

/* User ID */
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u");
$stmt->execute(['u' => $username]);
$userId = (int) $stmt->fetchColumn();

if (!$userId) {
    die('Utilisateur introuvable');
}

/* Panier */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_to_cart'])) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO cart_items (user_id, movie_id)
            VALUES (:user_id, :movie_id)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'movie_id' => (int) $_POST['movie_id']
        ]);
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['remove_from_cart'])) {
        $stmt = $pdo->prepare("
            DELETE FROM cart_items
            WHERE user_id = :user_id AND movie_id = :movie_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'movie_id' => (int) $_POST['movie_id']
        ]);
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['empty_cart'])) {
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id")
            ->execute(['user_id' => $userId]);
        header('Location: cart.php');
        exit;
    }
}

/* Films */
$stmt = $pdo->prepare("
    SELECT m.id, m.title, m.price
    FROM cart_items ci
    JOIN movies m ON m.id = ci.movie_id
    WHERE ci.user_id = :user_id
");
$stmt->execute(['user_id' => $userId]);
$cartMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartTotal = 0;
foreach ($cartMovies as $movie) {
    $cartTotal += (float) $movie['price'];
}

/* Anciennes commandes */
$stmt = $pdo->prepare("
    SELECT id, total_amount, created_at
    FROM orders
    WHERE user_id = :user_id
    ORDER BY created_at DESC
");
$stmt->execute(['user_id' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Mon panier</h1>
    <?php if (empty($cartMovies)) : ?>
        <p>Votre panier est vide.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($cartMovies as $movie) : ?>
                <li>
                    <?= htmlspecialchars($movie['title']) ?>
                    — <?= htmlspecialchars($movie['price']) ?> €

                    <form method="post" style="display:inline">
                        <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                        <button type="submit" name="remove_from_cart">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Total :</strong> <?= number_format($cartTotal, 2) ?> €</p>

        <form method="post">
            <button type="submit" name="empty_cart">Vider le panier</button>
        </form>
    <?php endif; ?>

    <hr>

    <h2>Anciennes commandes</h2>

    <?php if (empty($orders)) : ?>
        <p>Aucune commande passée.</p>
    <?php else : ?>
        <?php foreach ($orders as $order) : ?>
            <section>
                <h3>
                    Commande #<?= (int) $order['id'] ?>
                    — <?= htmlspecialchars($order['created_at']) ?>
                </h3>

                <p>Total : <?= htmlspecialchars($order['total_amount']) ?> €</p>

                <ul>
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT m.title, oi.price
                        FROM order_items oi
                        JOIN movies m ON m.id = oi.movie_id
                        WHERE oi.order_id = :order_id
                    ");
                    $stmt->execute(['order_id' => $order['id']]);
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) :
                    ?>
                        <li>
                            <?= htmlspecialchars($item['title']) ?>
                            — <?= htmlspecialchars($item['price']) ?> €
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>