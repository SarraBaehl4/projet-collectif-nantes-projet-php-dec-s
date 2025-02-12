<?php
require 'config.php';

// Vérifier si un ID de collecte est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: collection_list.php");
    exit;
}

$id = $_GET['id'];

// Récupérer les informations de la collecte
$stmt = $pdo->prepare("SELECT * FROM collectes WHERE id = ?");
$stmt->execute([$id]);
$collecte = $stmt->fetch();

if (!$collecte) {
    header("Location: collection_list.php");
    exit;
}

// Récupérer la liste des bénévoles
$stmt_benevoles = $pdo->prepare("SELECT id, nom FROM benevoles ORDER BY nom");
$stmt_benevoles->execute();
$benevoles = $stmt_benevoles->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ajoute un champ supplémentaire après chaque soumission
    if (isset($_POST["ajouter_dechet"])) {
        $_POST["dechet"][] = "";
        $_POST["quantite"][] = "";
    }
    // Mise à jour de la collecte
    $stmt = $pdo->prepare("UPDATE collectes SET date_collecte = ?, lieu = ?, id_benevole = ? WHERE id = ?");
    $stmt->execute([$_POST["date"], $_POST["lieu"], $_POST["benevole"], $id]);
    // Insérer plusieurs déchets
    if (!empty($_POST["dechet"]) && !empty($_POST["quantite"])) {
        $stmt = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");
        foreach ($_POST["dechet"] as $key => $dechet) {
            $quantite = $_POST["quantite"][$key];
            if (is_numeric($quantite) && $quantite > 0) {
                $stmt->execute([$id, $dechet, $quantite]);
            }
        }
    }
    header("Location: collection_list.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une collecte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="flex h-screen">
        <!-- Dashboard -->
        <div class="bg-cyan-200 text-white w-64 p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>

            <div class="mt-6">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md">
                    Déconnexion
                </button>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-blue-900 mb-6">Modifier une collecte</h1>

            <!-- Formulaire -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date :</label>
                        <input type="date" name="date" value="<?= htmlspecialchars($collecte['date_collecte']) ?>" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lieu :</label>
                        <input type="text" name="lieu" value="<?= htmlspecialchars($collecte['lieu']) ?>" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bénévole :</label>
                        <select name="benevole" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="" disabled selected>Sélectionnez un·e bénévole</option>
                            <?php foreach ($benevoles as $benevole): ?>
                                <option value="<?= $benevole['id'] ?>" <?= $benevole['id'] == $collecte['id_benevole'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($benevole['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Dechets :</label>

                        <?php
                        for ($i = 0; $i < 5; $i++):
                            $dechetValue = $_POST["dechet"][$i] ?? "";
                            $quantiteValue = $_POST["quantite"][$i] ?? "";
                        ?>
                            <div class="flex space-x-4 mt-2">
                                <select name="dechet[]"
                                    class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option> Selectionne un type de déchet </option>
                                    <option value="papier" <?= $dechetValue == "papier" ? "selected" : "" ?>>papier</option>
                                        <option value="plastique" <?= $dechetValue == "plastique" ? "selected" : "" ?>>plastique</option>
                                        <option value="metal" <?= $dechetValue == "metal" ? "selected" : "" ?>>métal</option>
                                        <option value="organique" <?= $dechetValue == "organique" ? "selected" : "" ?>>organique</option>
                                        <option value="verre" <?= $dechetValue == "verre" ? "selected" : "" ?>>verre</option>
                                </select>
                                <input type="number" step="0.01" name="quantite[]" value="<?= htmlspecialchars($quantiteValue) ?>"class="p-2 border border-gray-300 rounded-lg w-24" placeholder="kg">
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <a href="collection_list.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</a>
                        <button type="submit" class="bg-cyan-200 text-white px-4 py-2 rounded-lg">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>