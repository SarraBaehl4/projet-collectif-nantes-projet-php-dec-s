<?php
require 'config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=gestion_collectes", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Commencer une transaction
        $pdo->beginTransaction();

        try {
            // Désactiver temporairement les vérifications de clés étrangères
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

            // Supprimer le bénévole
            $stmt = $pdo->prepare("DELETE FROM benevoles WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Réactiver les vérifications de clés étrangères
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

            // Valider la transaction
            $pdo->commit();

            header("Location: volunteer_list.php?success=1");
            exit();
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            throw $e;
        }
    } catch (PDOException $e) {
        die("Erreur: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}
?>