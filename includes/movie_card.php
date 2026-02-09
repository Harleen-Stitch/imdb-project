<!-- Affichage des films dans category, director, index et search -->
<article class="movie-card">
    <h2><?= htmlspecialchars($movie['title']) ?></h2>

    <?php if (!empty($movie['poster_url'])) : ?>
        <img
            src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['poster_url']) ?>"
            alt="<?= htmlspecialchars($movie['title']) ?>"
        >
    <?php endif; ?>

    <div class="movie-buy">
        <span class="price">
            <?= htmlspecialchars($movie['price']) ?> €
        </span>

        <form action="cart.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
            <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
            <form method="post" action="cart.php">
                <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                <button type="submit" name="add_to_cart">Ajouter au panier</button>
            </form>
        </form>
    </div>

    <a href="movie.php?id=<?= (int) $movie['id'] ?>">
        Voir
    </a>
</article>