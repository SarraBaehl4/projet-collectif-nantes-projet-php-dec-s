<?php
require 'config.php';
require 'theme.php';

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$stmt = $pdo->query("
    SELECT b.id, b.nom, b.email, b.role, COALESCE(SUM(d.quantite_kg), 0) as quantite_totale_benevole
    FROM benevoles b
    LEFT JOIN collectes c ON b.id = c.id_benevole
    LEFT JOIN dechets_collectes d ON c.id = d.id_collecte
    GROUP BY b.id
    ORDER BY b.nom
");
$benevoles = $stmt->fetchAll(PDO::FETCH_ASSOC);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
                <ul role="list">
                    <li role="listitem"><a href="collection_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-list mr-3"></i> Liste des collectes</a></li>
                    <li role="listitem"><a href="collection_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                    <li role="listitem"><a href="volunteer_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                    <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                    <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>
                </ul>
            <div class="mt-6">
                <button onclick="logout()"
                    class="<?=$theme['logout']?>">
                    Déconnexion
                </button>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 p-8 overflow-y-auto">
            <!-- Titre -->
            <h1 class="<?=$theme['h1']?>">Liste des Bénévoles</h1>

            <!-- Tableau des bénévoles -->
            <div class="overflow-hidden  <?=$theme['tableBg']?>">
                <table class="w-full table-auto border-collapse">
                    <thead class="<?=  $theme['tableHeader']?>">
                        <tr>
                            <th class="py-3 px-4 text-left">Nom</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Rôle</th>
                            <th class="py-3 px-4 text-left">Total déchets collectés</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <?php foreach ($benevoles as $benevole): ?>
                            <tr class="hover:<?=  $theme['bgColor']?> transition duration-200">
                                <td class="py-3 px-4">
                                    <?= $benevole['nom'] ? htmlspecialchars($benevole['nom']) : 'Aucun bénévole' ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= $benevole['email'] ? htmlspecialchars($benevole['email']) : 'Aucun bénévole' ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= $benevole['role'] ? htmlspecialchars($benevole['role']) : 'Aucun bénévole' ?>
                                </td>
                                <td class="py-3 px-4"><?= number_format($benevole['quantite_totale_benevole'], 1) ?> kg</td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <a href="volunteer_edit.php?id=<?= $benevole['id'] ?>"
                                        class="<?=  $theme['buttons']?>">
                                        ✏️ Modifier
                                    </a>
                                    <a href="volunteer_delete.php?id=<?= $benevole['id'] ?>"
                                        class="<?=  $theme['deleteButton']?>"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette benevole ?');">
                                        🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>