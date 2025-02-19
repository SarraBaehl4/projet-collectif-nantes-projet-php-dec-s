<?php
require 'config.php';
require 'theme.php';
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
<body class="<?=$theme['bgColor']?> && <?=$theme['textColor']?>">
<div class="flex h-screen">

    <!-- Barre de navigation -->
    <nav class="<?=$theme['associationName']?>">
        <h2 class="text-6xl font-bold mb-6">Littoral Propre</h2>
            <ul role="list">
                <li role="listitem"><a href="collection_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-list mr-3"></i> Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                <li role="listitem"><a href="volunteer_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
        <div class="mt-6">
            <button onclick="logout()" class="<?=$theme['logout']?>" aria-label="Déconnexion">
                Déconnexion
            </button>
        </div>
    </nav>

    <!-- Contenu principal -->
    <section class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="<?=$theme['h1']?>">Mon compte</h1>

        <!-- Message de succès ou d'erreur -->
        <div class="text-green-600 text-center mb-4" id="success-message" style="display:none;">
            Vos paramètres ont été mis à jour avec succès.
        </div>
        <div class="text-red-600 text-center mb-4" id="error-message" style="display:none;">
            Le mot de passe actuel est incorrect.
        </div>

        <form id="settings-form" class="space-y-6">
            <!-- Champ Email -->
            <div>
                <label for="email" class="block text-sm font-medium <?=$theme['textColor']?>">Email</label>
                <input type="email" name="email" id="email" value="exemple@domaine.com" required
                       class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Mot de passe actuel -->
            <div>
                <label for="current_password" class="block text-sm font-medium <?=$theme['textColor']?>">Mot de passe
                    actuel</label>
                <input type="password" name="current_password" id="current_password" required
                       class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Nouveau Mot de passe -->
            <div>
                <label for="new_password" class="block text-sm font-medium <?=$theme['textColor']?>">Nouveau mot de passe</label>
                <input type="password" name="new_password" id="new_password"
                       class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Confirmer le nouveau Mot de passe -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium <?=$theme['textColor']?>">Confirmer le mot de
                    passe</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Boutons -->
            <div class="flex justify-between items-center">
                <a href="collection_list.php" class="text-sm text-blue-600 hover:underline">Retour à la liste des
                    collectes</a>
                <button type="button" onclick="updateSettings()"
                        class="<?=$theme['buttons']?>">
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

