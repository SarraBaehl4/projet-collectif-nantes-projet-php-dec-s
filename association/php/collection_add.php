<?php
require 'config.php';
require 'theme.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Récupérer la liste des bénévoles
$stmt_benevoles = $pdo->query("SELECT id, nom FROM benevoles ORDER BY nom");
$stmt_benevoles->execute();
$benevoles = $stmt_benevoles->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $lieu = $_POST["lieu"];
    $benevole_id = $_POST["benevole"];  // ID du bénévole choisi, modifié ici pour correspondre au formulaire

    // Insérer la collecte avec le bénévole sélectionné
    $stmt = $pdo->prepare("INSERT INTO collectes (date_collecte, lieu, id_benevole) VALUES (?, ?, ?) ");
    if (!$stmt->execute([$date, $lieu, $benevole_id])) {
        die('Erreur lors de l\'insertion dans la base de données.');
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
    <title>Ajouter une collecte</title>
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
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-tachometer-alt mr-3"></i>
                        Liste des collectes</a></li>
                <li role="listitem"><a href="collection_add.php"
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fas fa-plus-circle mr-3"></i>
                        Ajouter une collecte</a></li>
                <li role="listitem"><a href="volunteer_list.php"
                        class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i class="fa-solid fa-list mr-3"></i>
                        Liste des bénévoles</a></li>
                <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
            </ul>
        <div class="mt-6">
            <button onclick="logout()" class="<?=$theme['logout']?>" aria-label="Déconnexion">
                Déconnexion
            </button>
        </div>
    </div>

        <!-- Contenu principal -->
        <section class="flex-1 p-8 overflow-y-auto">
            <!-- Titre -->
            <h1 class="<?=$theme['h1']?>">Ajouter une collecte</h1>

        <!-- Formulaire -->
        <div class="<?=$theme['tableBg']?> p-6 ">
            <form method="POST" class="space-y-4">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium <?=$theme['textColor']?>">Date :</label>
                    <input type="date" name="date" required
                           class="w-full p-2 border border-gray-300  focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Lieu -->
                <div>
                    <label class="block text-sm font-medium <?=$theme['textColor']?>">Lieu :</label>
                    <input type="text" name="lieu" required
                           class="w-full p-2 border border-gray-300  focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Bénévole responsable -->
                <div>
                    <label class="block text-sm font-medium <?=$theme['textColor']?>">Bénévole Responsable :</label>
                    <select name="benevole" required
                            class="w-full p-2 border border-gray-300  focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner un bénévole</option>
                        <?php foreach ($benevoles as $benevole): ?>
                            <option value="<?= $benevole['id'] ?>" <?= $benevole['id'] ==  'selected' ?>>
                                <?= htmlspecialchars($benevole['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4">
                    <a href="collection_list.php" class="bg-red-700 hover:bg-red-800 text-cyan-50 px-4 py-2">Annuler</a>
                    <button type="submit" class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2">
                        ➕ Ajouter

                        </button>
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