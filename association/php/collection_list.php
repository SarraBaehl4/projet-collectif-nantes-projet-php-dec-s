<?php
require 'config.php';

try {
    $limit = 5;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $stmt = $pdo->prepare("
        SELECT c.id, c.date_collecte, c.lieu, b.nom,
        (SELECT SUM(d.quantite_kg)
                FROM dechets_collectes d
                WHERE d.id_collecte = c.id) as total_dechets
        FROM collectes c
        LEFT JOIN benevoles b ON c.id_benevole = b.id
        ORDER BY c.date_collecte DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $collectes = $stmt->fetchAll();
    $totalDechetsStmt = $pdo->query("
    SELECT SUM(d.quantite_kg) as total_general
    FROM dechets_collectes d
    INNER JOIN collectes c ON c.id = d.id_collecte
");

    $totalDechets = $totalDechetsStmt->fetch(PDO::FETCH_ASSOC);
    $total_dechets = $totalDechets['total_general'] ?? 0;


    if (!empty($collectes)) {
        $derniereCollecteId = $collectes[0]['id'];
        $dechetsStmt = $pdo->prepare("
        SELECT d.type_dechet, SUM(d.quantite_kg) as quantite_totale
        FROM dechets_collectes d
        INNER JOIN collectes c ON c.id = d.id_collecte
        WHERE c.id = :id
        GROUP BY d.type_dechet
    ");

        $dechetsStmt->execute(['id' => $derniereCollecteId]);
        $dechets = $dechetsStmt->fetchAll(PDO::FETCH_ASSOC);
        $derniere_collecte_total = 0;
        foreach ($dechets as $dechet) {
            if (isset($dechet['quantite_totale'])) {
                $derniere_collecte_total += $dechet['quantite_totale'];
            }
        }
    }
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

<section class="bg-gray-100 text-gray-900">
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
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2"
                    aria-label="Déconnexion">
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

            <!-- Cartes d'informations -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Nombre total de collectes -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= count($collectes) ?></p>
                </div>
                <!-- Bénévole Responsable -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Poids Total des Collectes</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= number_format($total_dechets, 2) ?> kg</p>
                </div>
                <!-- Dernière collecte -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
                    <p class="text-lg text-gray-600"><?= htmlspecialchars($collectes[0]['lieu']) ?></p>
                    <p class="text-lg text-gray-600"><?= date('d/m/Y', strtotime($collectes[0]['date_collecte'])) ?></p>
                    <p class="text-lg text-gray-600"><?= number_format($derniere_collecte_total, 2) ?> kg</p>
                </div>
            </div>

            <!-- Tableau des collectes -->
            <div class="overflow-hidden rounded-lg shadow-lg bg-white">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-blue-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-left">Lieu</th>
                            <th class="py-3 px-4 text-left">Bénévole Responsable</th>
                            <th class="py-3 px-4 text-left">Total déchets collectés</th>
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
                                <td class="py-3 px-4"><?= number_format($collecte['total_dechets'] ?? 0, 2) ?> kg</td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <a href="collection_details.php?id=<?= $collecte['id'] ?>"
                                        class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                        📄Details
                                    </a>
                                    <a href="collection_edit.php?id=<?= $collecte['id'] ?>"
                                        class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                        ✏️ Modifier
                                    </a>
                                    <a href="collection_delete.php?id=<?= $collecte['id'] ?>"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette collecte ?');">
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
    </div>
</section>
</body>

</html>