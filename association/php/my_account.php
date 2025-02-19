<?php
session_start();

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';
require 'theme.php';

$message = '';
$messageType = '';

// Récupérer l'email actuel de l'utilisateur
$stmt = $pdo->prepare("SELECT email FROM benevoles WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';

    // Vérifications
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword) || empty($email)) {
        $message = 'Tous les champs sont obligatoires';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Les nouveaux mots de passe ne correspondent pas';
        $messageType = 'error';
    } else {
        // Vérifier le mot de passe actuel
        $stmt = $pdo->prepare('SELECT mot_de_passe FROM benevoles WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData || !password_verify($currentPassword, $userData['mot_de_passe'])) {
            $message = 'Le mot de passe actuel est incorrect';
            $messageType = 'error';
        } else {
            // Mettre à jour le mot de passe et l'email
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE benevoles SET mot_de_passe = ?, email = ? WHERE id = ?');

            try {
                $stmt->execute([$hashedPassword, $email, $_SESSION['user_id']]);
                $message = 'Vos paramètres ont été mis à jour avec succès';
                $messageType = 'success';

                // Mise à jour de l'email affiché après la mise à jour
                $user['email'] = $email;

            } catch (PDOException $e) {
                $message = 'Erreur lors de la mise à jour : ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>

<body class="<?= $theme['bgColor'] ?> <?= $theme['textColor'] ?>">
    <div class="flex h-screen">
        <!-- Barre de navigation -->
        <nav class="<?= $theme['associationName'] ?>">
            <h2 class="text-6xl font-bold mb-6">Littoral Propre</h2>
            <ul role="list">
                <li role="listitem"><a href="collection_list.php"
                        class="flex items-center py-2 px-3 <?= $theme['hoverColorSidebar'] ?>"><i
                            class="fas fa-list mr-3"></i> Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php"
                        class="flex items-center py-2 px-3 <?= $theme['hoverColorSidebar'] ?>"><i
                            class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php"
                        class="flex items-center py-2 px-3 <?= $theme['hoverColorSidebar'] ?>"><i
                            class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                <li role="listitem"><a href="user_add.php"
                        class="flex items-center py-2 px-3 <?= $theme['hoverColorSidebar'] ?>"><i
                            class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php"
                        class="flex items-center py-2 px-3 <?= $theme['hoverColorSidebar'] ?>"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
            <div class="mt-6">
                <button onclick="logout()" class="<?= $theme['logout'] ?>" aria-label="Déconnexion">
                    Déconnexion
                </button>
            </div>
        </nav>

        <!-- Contenu principal -->
        <section class="flex-1 p-8 overflow-y-auto">
            <h1 class="<?= $theme['h1'] ?>">Mon compte</h1>

            <!-- Affichage du message -->
            <?php if (!empty($message)): ?>
                <div
                    class="<?= $messageType === 'success' ? 'text-green-600' : 'text-red-600' ?> text-center mb-4 text-xl font-bold">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium <?= $theme['textColor'] ?>">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="current_password" class="block text-sm font-medium <?= $theme['textColor'] ?>">Mot de
                        passe actuel</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium <?= $theme['textColor'] ?>">Nouveau mot
                        de passe</label>
                    <input type="password" name="new_password" id="new_password" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium <?= $theme['textColor'] ?>">Confirmer
                        le mot de passe</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-between items-center">
                    <a href="collection_list.php" class="<?= $theme['buttons'] ?>"
                        aria-label="Retour à la liste des collectes" title="Retour à la liste des collectes">
                        Retour à la liste des collectes
                    </a>

                    <button type="submit" class="<?= $theme['buttons'] ?>">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>

</html>