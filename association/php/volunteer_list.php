<?php
require 'config.php';
require 'theme.php';

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
try {
    $limit = 5;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Requête pour récupérer les bénévoles avec pagination
    $stmt = $pdo->prepare("
        SELECT b.id, b.nom, b.email, b.role, COALESCE(SUM(d.quantite_kg), 0) as quantite_totale_benevole
        FROM benevoles b
        LEFT JOIN collectes c ON b.id = c.id_benevole
        LEFT JOIN dechets_collectes d ON c.id = d.id_collecte
        GROUP BY b.id
        ORDER BY b.nom
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $benevoles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer le nombre total de bénévoles pour la pagination
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM benevoles");
    $totalBenevoles = $totalStmt->fetchColumn();
    $totalPages = ceil($totalBenevoles / $limit);
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

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
        <nav class="<?=$theme['associationName']?>">
            <h2 class="text-6xl font-bold mb-6">Littoral Propre</h2>
            <ul role="list">
                <li role="listitem"><a href="collection_list.php"
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-list mr-3"></i>
                        Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php"
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-plus-circle mr-3"></i>
                        Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php"
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fa-solid fa-list mr-3"></i>
                        Liste des bénévoles</a></li>
                <li role="listitem"><a href="volunteer_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
            <div class="mt-6">
                <button onclick="logout()" class="<?=$theme['logout']?>"
                    aria-label="Déconnexion">
                    Déconnexion
                </button>
            </div>
        </nav>

        <!-- Contenu principal -->
        <section class="flex-1 p-8 overflow-y-auto">
            <!-- Titre -->
            <h1 class="<?=$theme['h1']?>">Liste des Bénévoles</h1>

            <!-- Tableau des bénévoles -->
            <div class="overflow-hidden  <?=$theme['tableBg']?>">
                <table class="w-full table-auto border-collapse">
                    <thead class="<?=$theme['tableHeader']?>">
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
                            <tr class="hover:<?=$theme['bgColor']?> transition duration-200">
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
            <!-- Pagination -->
            <div class="mt-6 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-4 py-2 border <?= $i == $page ? 'bg-cyan-600 text-cyan-50' : 'bg-gray-200 text-gray-800' ?>hover:bg-blue-400 transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
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