<?php
require 'config.php';
require 'theme.php';
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
    // Gestion des déchets
    if (!empty($_POST["dechet"]) && !empty($_POST["quantite"])) {
        // Préparer les requêtes
        $checkStmt = $pdo->prepare("SELECT id FROM dechets_collectes WHERE id_collecte = ? AND type_dechet = ?");
        $updateStmt = $pdo->prepare("UPDATE dechets_collectes SET quantite_kg = ? WHERE id_collecte = ? AND type_dechet = ?");
        $insertStmt = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");

        foreach ($_POST["dechet"] as $key => $dechet) {
            $quantite = $_POST["quantite"][$key];

            // Vérifier que la quantité est valide
            if (!is_numeric($quantite) || $quantite <= 0) {
                continue;
            }

            // Vérifier si le déchet existe déjà pour cette collecte
            $checkStmt->execute([$id, $dechet]);
            $exists = $checkStmt->fetch();

            if ($exists) {
                // Mise à jour du déchet existant
                $updateStmt->execute([$quantite, $id, $dechet]);
            } else {
                // Insertion d'un nouveau déchet
                $insertStmt->execute([$id, $dechet, $quantite]);
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
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>

<body class="?=$theme['bgColor']?> && <?= $theme['textColor'] ?>">

    <div class="flex h-screen">
        <!-- Dashboard -->
        <nav class="<?=$theme['associationName']?>">
            <h2 class="text-6xl font-bold mb-6">Littoral Propre</h2>
            <ul role="list">
                <li role="listitem"><a href="collection_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-list mr-3"></i> Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>
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
            <h1 class="<?= $theme['h1'] ?>">Modifier une collecte</h1>

            <!-- Formulaire -->
            <div class="<?= $theme['tableBg'] ?> p-6">
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium <?= $theme['textColor'] ?>">Date :</label>
                        <input type="date" name="date" value="<?= htmlspecialchars($collecte['date_collecte']) ?>"
                            required class="w-full p-2 border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium <?= $theme['textColor'] ?>">Lieu :</label>
                        <input type="text" name="lieu" value="<?= htmlspecialchars($collecte['lieu']) ?>" required
                            class="w-full p-2 border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium <?= $theme['textColor'] ?>">Bénévole :</label>
                        <select name="benevole" required class="w-full p-2 border border-gray-300">
                            <option value="" disabled selected>Sélectionnez un·e bénévole</option>
                            <?php foreach ($benevoles as $benevole): ?>
                                <option value="<?= $benevole['id'] ?>" <?= $benevole['id'] == $collecte['id_benevole'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($benevole['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block <?= $theme['textColor'] ?> font-medium">Dechets :</label>

                        <?php
                        for ($i = 0; $i < 5; $i++):
                            $dechetValue = $_POST["dechet"][$i] ?? "";
                            $quantiteValue = $_POST["quantite"][$i] ?? "";
                            ?>
                            <div class="flex space-x-4 mt-2">
                                <select name="dechet[]"
                                    class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option> Selectionne un type de déchet </option>
                                    <option value="papier" <?= $dechetValue == "papier" ? "selected" : "" ?>>papier</option>
                                    <option value="plastique" <?= $dechetValue == "plastique" ? "selected" : "" ?>>plastique
                                    </option>
                                    <option value="metal" <?= $dechetValue == "metal" ? "selected" : "" ?>>métal</option>
                                    <option value="organique" <?= $dechetValue == "organique" ? "selected" : "" ?>>organique
                                    </option>
                                    <option value="verre" <?= $dechetValue == "verre" ? "selected" : "" ?>>verre</option>
                                </select>
                                <input type="number" step="0.01" name="quantite[]"
                                    value="<?= htmlspecialchars($quantiteValue) ?>" class="p-2 border border-gray-300 w-24"
                                    placeholder="kg">
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <a href="collection_list.php" class="bg-gray-500 text-white px-4 py-2">Annuler</a>
                        <button type="submit" class="<?= $theme['buttons'] ?>">Modifier</button>
                    </div>
                </form>
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