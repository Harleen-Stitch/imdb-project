<?php
require_once dirname(__DIR__) . '/includes/config.php'; # charge .env
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/security.php';
startSecureSession();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>IMDB Project</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>

<nav class="header">
    <ul class="menu">
        <li><a href="index.php">Accueil</a></li>

        <li class="dropdown">
            <span>Catégories</span>
            <ul class="dropdown-menu">
                <li><a href="category.php?name=Action">Action</a></li>
                <li><a href="category.php?name=Aventure">Aventure</a></li>
                <li><a href="category.php?name=Animation">Animation</a></li>
                <li><a href="category.php?name=Comedy">Comédie</a></li>
                <li><a href="category.php?name=Crime">Crime</a></li>
                <li><a href="category.php?name=Documentary">Documentaire</a></li>
                <li><a href="category.php?name=Drama">Drame</a></li>
                <li><a href="category.php?name=Family">Familial</a></li>
                <li><a href="category.php?name=Fantasy">Fantastique</a></li>
                <li><a href="category.php?name=History">Histoire</a></li>
                <li><a href="category.php?name=Horror">Horreur</a></li>
                <li><a href="category.php?name=Music">Musique</a></li>
                <li><a href="category.php?name=Mystery">Mystère</a></li>
                <li><a href="category.php?name=Romance">Romance</a></li>
                <li><a href="category.php?name=Science%20Fiction">Science-fiction</a></li>
                <li><a href="category.php?name=TV%20Movie">Téléfilm</a></li>
                <li><a href="category.php?name=Thriller">Thriller</a></li>
                <li><a href="category.php?name=War">Guerre</a></li>
                <li><a href="category.php?name=Western">Western</a></li>
            </ul>
        </li>

        <li><a href="search.php">Recherche</a></li>
        <li><a href="cart.php">Panier</a></li>
        <li>
            <?php if (!isLoggedIn()) { ?>
                <a class="btn" href="<?= APP_URL ?>/public/login.php">Se connecter</a>
            <?php } else { ?>
                <!-- bouton  car sinon ça fait un get, et on ne veut pas -->
                <form action="<?= APP_URL ?>/public/logout.php" method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <button type="submit" class="btn">Se déconnecter</button>
                </form>
            <?php } ?>
        </li>
        <li>
            <?php if (isLoggedIn()) { ?>
                <form action="<?= APP_URL ?>/public/account.php" method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <button type="submit" class="btn">Mon compte</button>
                </form>
            <?php } ?>
        </li>
    </ul>
</nav>