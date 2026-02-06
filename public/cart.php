<?php

# require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
getCurrentUser();

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Panier</h1>
    <p>Panier vide</p>

    <table>
        <tr>
            <th>Film</th>
            <th>Prix</th>
            <th>Action</th>
        </tr>
    </table>




</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>