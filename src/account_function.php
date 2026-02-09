<?php 
// psrc/account_function.php
// permet d'appeler les classes et sera valable pour tous les fichiers

require_once dirname(__DIR__) . '/includes/db.php'; # BDD

function deleteUserById(int $userId): void {
    global $pdo;

    $pdo->beginTransaction();
    try {
        $stmt =$pdo->prepare("DELETE FROM users WHERE id= :uid");
        $stmt->execute([':uid' => $userId]);
        
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}