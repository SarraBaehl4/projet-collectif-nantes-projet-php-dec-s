<nav?php
require 'config.php';

// Vérifier si un ID du bénévole est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: volunteer_list.php");
    exit;
}

$id = $_GET['id'];

// Récupérer les informations des bénévoles
$stmt = $pdo->prepare("SELECT * FROM benevoles WHERE id = ?");
$stmt->execute([$id]);
$benevole = $stmt->fetch();

if (!$benevole) {
    header("Location: volunteer_list.php");
    exit;
}


// Mettre à jour la liste des bénévoles
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $role = $_POST["role"];

    $stmt = $pdo->prepare("UPDATE benevoles SET nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = $id");
    $stmt->execute([$nom, $email, $mot_de_passe, $role]);

    header("Location: volunteer_list.php");
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
        <nav class="bg-cyan-200 text-white w-64 p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
                <ul role="list">
                    <li role="listitem"><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-tachometer-alt mr-3"></i> Liste des collectes</a></li>
                    <li role="listitem"><a href="collection_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
                    <li role="listitem"><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
                    <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
                    <li role="listitem"><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>
                </ul>
            <div class="mt-6">
                <button onclick="logout()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2" aria-label="Déconnexion">
                    Déconnexion
                </button>
            </div>
</nav>

        <!-- Contenu principal -->
        <section class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-blue-900 mb-6">Modifier un bénévole </h1>

            <!-- Formulaire -->
            <div class="bg-white p-6">
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom :</label>
                        <input type="nom" name="nom" value="<?= htmlspecialchars($benevole['nom']) ?>"
                            required class="w-full p-2 border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">email :</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($benevole['email']) ?>" required
                            class="w-full p-2 border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">mot de passe :</label>
                        <input type="password" name="mot_de_passe" value="<?= htmlspecialchars($benevole['mot_de_passe']) ?>"
                            required class="w-full p-2 border border-gray-300">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Rôle</label>
                        <select name="role"
                            class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="participant">Participant</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                   
                    <div class="flex justify-end space-x-4">
                        <a href="volunteer_list.php" class="bg-gray-500 text-white px-4 py-2">Annuler</a>
                        <button type="submit" class="bg-cyan-200 text-white px-4 py-2">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
</section>
<script>
function logout() {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        window.location.href = 'logout.php';
    }
}
</script>
</body>

</html>