<?php
// public/cart.php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php'; # autoloader composer
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/src/movie_functions.php';
require_once dirname(__DIR__) . '/src/cart_functions.php';
require_once dirname(__DIR__) . '/includes/security.php';
startSecureSession();
requireLogin();

/* User ID */
$userId = $_SESSION['user_id'];
$error = null;

if (!$userId) {
    die('Utilisateur introuvable');
}

/* Panier */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf_token)){
        $error = "Une erreur est survenue";
        error_log("Tentative CSRF détectée sur cart.php");
    } else {
        if (isset($_POST['add_to_cart'])) {
            $movieId = filter_input(INPUT_POST, "movie_id", FILTER_VALIDATE_INT);
            if ($movieId === false || $movieId === null) {
                $error = "ID de film invalide.";
            } else {
                addToCart($userId, $movieId);
                header('Location: cart.php');
                exit;
            }
        }   

        if (isset($_POST['remove_from_cart'])) {
            $movieId = filter_input(INPUT_POST, "movie_id", FILTER_VALIDATE_INT);
            if ($movieId === false || $movieId === null) {
                $error = "ID de film invalide.";
            } else {
                removeFromCart($userId, $movieId);
                header('Location: cart.php');
                exit;
            }
        }

        if (isset($_POST['empty_cart'])) {
            emptyCart($userId);
            header('Location: cart.php');
            exit;
        }

        if (isset($_POST['checkout'])) { 
            try {
                $orderId = checkout($userId);
                $_SESSION['flash_success'] = 'Commande validée !';
                header('Location: cart.php');
                exit;
            } catch (Throwable $e) {
                $error = "Impossible de finaliser la commande. Veuillez réesaayer.";
                error_log("Checkout error for user {$userId}: " . $e->getMessage());
            }
        }
    }
}

/* Films */
$cartMovies = getCartMovies($userId);
$cartTotal = array_sum(array_map(fn($m) => (float)$m['price'], $cartMovies));

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
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']); // fenêtre pop-up ne s'affiche qu'une fois
?>

<main>
    <?php if (!empty($flashSuccess)) : ?>
        <div id="order-modal" class="modal" aria-hidden="false" role="dialog" aria-labelledBy="order-modal-titel">
            <div class="modal-content">
                <h3 id="order-modal-titel">Commande validée !</h3>
                <p> Votre commande a été enregistrée avec succès.</p>
                <button id="order-modal-close" type="button" aria-label="Fermer"><a href="index.php">OK</a></button>

            </div>
        </div>
        <?php endif; ?>
    <h1>Mon panier</h1>

    <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (empty($cartMovies)) : ?>
        <p>Votre panier est vide.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($cartMovies as $movie) : ?>
                <li>
                    <?= htmlspecialchars($movie['title']) ?>
                    <?= number_format((float)$movie['price'], 2) ?> €

                    <form method="post" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                        <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                        <button type="submit" name="remove_from_cart">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Total :</strong> <?= number_format((float)$cartTotal, 2) ?> €</p>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
            <button type="submit" name="empty_cart">Vider le panier</button>
        </form>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
            <button type="submit" name="checkout">Passer commande</button>
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

                <p>Total : <?= number_format((float)$order['total_amount']) ?> €</p>

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
                            <?= number_format((float)$item['price']) ?> €
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>