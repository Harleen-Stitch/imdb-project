<?php 

# require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
getCurrentUser();

include dirname(__DIR__) . '/includes/header.php';
?>

<main>
    <h1>Mes commandes</h1>
    <p>Aucune commande</p>

    <table>
        <tr>
            <th>Date</th>
            <th>Total</th>
        </tr>
    </table>

</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>