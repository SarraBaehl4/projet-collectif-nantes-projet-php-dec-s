<?php
require 'config.php';

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $pdo->prepare("
    SELECT d.type_dechet, SUM(d.quantite_kg) as quantite_totale
    FROM dechets_collectes d
    INNER JOIN collectes c ON c.id = d.id_collecte
    WHERE c.id = :id
    GROUP BY d.type_dechet
");

$stmt->execute(['id' => $id]);
$dechets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total de tous les déchets
$total_dechets = 0;
foreach ($dechets as $dechet) {
    if (isset($dechet['quantite_totale'])) {
        $total_dechets += $dechet['quantite_totale'];
    }
}

//recuperer date et lieu de la collecte:
$stmt_collecte = $pdo->prepare("SELECT date_collecte, lieu FROM collectes WHERE id=?");
$stmt_collecte->execute([$id]);
$collecte = $stmt_collecte->fetch(PDO::FETCH_ASSOC);


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Collectes</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Lora:wght@400;700&family=Montserrat:wght@300;400;700&family=Open+Sans:wght@300;400;700&family=Poppins:wght@300;400;700&family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;700&family=Nunito:wght@300;400;700&family=Merriweather:wght@300;400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Barre de navigation -->
        <div class="bg-cyan-200 text-white w-64 p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            <ul>
                <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-tachometer-alt mr-3"></i> Liste des collectes</a></li>
                <li><a href="collection_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                <li><a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
            <div class="mt-6">
                <button onclick="logout()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md">
                    Déconnexion
                </button>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 p-8 overflow-y-auto">
            <!-- Titre -->
            <h1 class="text-4xl font-bold text-blue-800 mb-6">Détail de la collecte</h1>
            <p>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2"><strong>Date :</strong>
                    <?= htmlspecialchars($collecte['date_collecte']) ?></th>
                </th>
            </tr>
            </p>
            <p>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2"><strong>Lieu :</strong>
                    <?= htmlspecialchars($collecte['lieu']) ?>
                </th>
            </tr>
            </p>
            <!-- Message de notification -->
            <?php if (isset($_GET['message'])): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>



            <!-- Cartes d'informations par type -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php foreach ($dechets as $dechet): ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">
                            Déchet <?= htmlspecialchars($dechet['type_dechet']) ?>
                        </h3>
                        <p class="text-3xl font-bold text-blue-600">
                            <?= number_format($dechet['quantite_totale'], 1) ?> kg
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Carte du total -->
            <div class="mb-8">
                <div class="bg-green-50 p-6 rounded-lg shadow-lg border-2 border-green-500">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        Total des déchets collectés
                    </h3>
                    <p class="text-4xl font-bold text-green-600">
                        <?= number_format($total_dechets, 1) ?> kg
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>