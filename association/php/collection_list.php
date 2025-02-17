<?php
require 'config.php';

try {
    $limit = 5;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("
        SELECT c.id, c.date_collecte, c.lieu, b.nom
        FROM collectes c
        LEFT JOIN benevoles b ON c.id_benevole = b.id
        ORDER BY c.date_collecte DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $collectes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = $pdo->prepare("SELECT nom FROM benevoles WHERE role = 'admin' LIMIT 1");
    $query->execute();
    $admin = $query->fetch(PDO::FETCH_ASSOC);
    $adminNom = $admin ? htmlspecialchars($admin['nom']) : 'Aucun administrateur trouvé';

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM collectes");
    $totalCollectes = $totalStmt->fetchColumn();
    $totalPages = ceil($totalCollectes / $limit);
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
    <title>Liste des Collectes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Barre de navigation -->
        <nav class="bg-cyan-200 text-white w-64 p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            <ul role="list">
                <li role="listitem"><a href="collection_list.php"
                        class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-tachometer-alt mr-3"></i>
                        Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php"
                        class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-plus-circle mr-3"></i>
                        Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php"
                        class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fa-solid fa-list mr-3"></i>
                        Liste des bénévoles</a></li>
                <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i
                            class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
            <div class="mt-6">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2" aria-label="Déconnexion">
                    Déconnexion
                </button>
            </div>
        </nav>

        <!-- Contenu principal -->
        <section class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-blue-800 mb-6">Liste des Collectes de Déchets</h1>

            <!-- Message de notification -->
            <?php if (isset($_GET['message'])): ?>
                <div class="bg-green-100 text-green-800 p-4 mb-6">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $totalCollectes ?></p>
                </div>
                <div class="bg-white p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
                    <?php if (!empty($collectes)): ?>
                        <p class="text-lg text-gray-600"><?= htmlspecialchars($collectes[0]['lieu']) ?></p>
                        <p class="text-lg text-gray-600"><?= date('d/m/Y', strtotime($collectes[0]['date_collecte'])) ?></p>
                    <?php else: ?>
                        <p class="text-lg text-gray-600">Aucune collecte disponible</p>
                    <?php endif; ?>
                </div>
                <div class="bg-white p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Bénévole Admin</h3>
                    <p class="text-lg text-gray-600"><?= $adminNom ?></p>
                </div>
            </div>

            <div class="overflow-hidden bg-white">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-blue-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-left">Lieu</th>
                            <th class="py-3 px-4 text-left">Bénévole Responsable</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <?php foreach ($collectes as $collecte): ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="py-3 px-4"><?= date('d/m/Y', strtotime($collecte['date_collecte'])) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($collecte['lieu']) ?></td>
                                <td class="py-3 px-4">
                                    <?= $collecte['nom'] ? htmlspecialchars($collecte['nom']) : 'Aucun bénévole' ?>
                                </td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="collection_details.php?id=<?= $collecte['id'] ?>"
                                        class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                        📄Details</a>
                                    <a href="collection_edit.php?id=<?= $collecte['id'] ?>"
                                        class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                        ✏️ Modifier
                                    </a>
                                    </a>
                                    <a href="collection_delete.php?id=<?= $collecte['id'] ?>"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette benevole ?');">
                                        🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- affichage pagination de la liste des collectes -->
            <div class="mt-6 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-4 py-2 border <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' ?>hover:bg-blue-400 transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </section>
    </div>
</body>

</html>