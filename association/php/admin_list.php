<?php
require 'config.php';
require 'theme.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Bénévoles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body class="<?=$theme['bgColor']?> && <?=$theme['textColor']?>">
<div class="flex h-screen">
    <!-- Barre de navigation -->
    <div class="<?=$theme['associationName']?>">
    <h2 class="text-6xl font-bold mb-6">Littoral Propre</h2>

            <li><a href="collection_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-list mr-3"></i> Liste des collectes</a></li>
            <li><a href="collection_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>

        <div class="mt-6">
            <button onclick="logout()" class="<?=$theme['logout']?>">
                Déconnexion
            </button>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="<?=$theme['h1']?>">Liste des Bénévoles</h1>

        <!-- Tableau des admin -->
        <div class="overflow-hidden <?=$theme['tableBg']?>">
            <table class="w-full table-auto border-collapse">
                <thead class="<?=  $theme['tableHeader']?>">
                <tr>
                    <th class="py-3 px-4 text-left">Nom</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Rôle</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                <tr class="hover:bg-gray-100 transition duration-200">
                    <td class="py-3 px-4">Nom de l'admin</td>
                    <td class="py-3 px-4">email@example.com</td>
                    <td class="py-3 px-4">Admin</td>
                    <td class="py-3 px-4 flex space-x-2">
                        <a href="#"
                           class="<?=  $theme['buttons']?>">
                            ✏️ Modifier
                        </a>
                        <a href="#"
                           class="<?=  $theme['deleteButton']?>">
                            🗑️ Supprimer
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

