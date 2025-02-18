<?php
require "config.php";
require "theme.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error_message = ""; // Variable pour stocker le message d'erreur

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $nom = $_POST["nom"];
        $email = $_POST["email"];
        $mot_de_passe = $_POST["mot_de_passe"];
        $role = $_POST["role"];

        // Verif si l'email saisi existe déjà
        $check = $pdo->prepare("SELECT COUNT(*) FROM benevoles WHERE email = ?");// Prépare une requête SQL pour compter les emails identiques
        $check->execute([$email]); // Exécute la requête avec l'email fourni
        if ($check->fetchColumn() > 0) { // Si le compte est supérieur à 0
            throw new PDOException("Cette adresse email est déjà utilisée.");// Lance une exception
        }

        // Insérer un nouveau bénévole
        $stmt = $pdo->prepare("INSERT INTO benevoles (nom, email, mot_de_passe, role) VALUES (?,?,?,?)");//prepare la requête d'insertion
        if (
            !$stmt->execute([
                $nom,
                $email,
                password_hash($mot_de_passe, PASSWORD_DEFAULT)
                ,
                $role
            ])
        ) { //exécute la requête avec les données du formulaire
            throw new PDOException("Erreur lors de l'insertion dans la base de données.");
        }

        // Si l'insertion réussit, redirige vers la page saisie formulaire
        header("Location: user_add.php");
        exit;

    } catch (PDOException $e) { // Capture les erreurs PDO
        $error_message = $e->getMessage(); // Stocke le message d'erreur
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Bénévole</title>
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
                <li role="listitem"><a href="user_add.php" class="flex items-center py-2 px-3 <?=$theme['hoverColorSidebar']?>"><i
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
            <h1 class="<?=$theme['h1']?>">Ajouter un Bénévole</h1>

            <!-- Formulaire d'ajout -->
            <div class="<?=$theme['tableBg']?> p-6 max-w-lg mx-auto">
                <form action="user_add.php" method="POST">
                    <div class="mb-4">
                        <label class="block <?=$theme['textColor']?> font-medium">Nom</label>
                        <input type="text" name="nom"
                            class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nom du bénévole" required>
                    </div>

                    <div class="mb-4">
                        <label class="block <?=$theme['textColor']?> font-medium">Email</label>
                        <input type="email" name="email"
                            class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Email du bénévole" required>
                        <?php if (!empty($error_message)): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?php echo htmlspecialchars($error_message); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="block <?=$theme['textColor']?> font-medium">Mot de passe</label>
                        <input type="password" name="mot_de_passe"
                            class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Mot de passe" required>
                    </div>

                    <div class="mb-4">
                        <label class="block <?=$theme['textColor']?> font-medium">Rôle</label>
                        <select name="role"
                            class="w-full mt-2 p-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="participant">Participant</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full bg-cyan-200 hover:bg-cyan-600 text-white py-3 font-semibold">
                            Ajouter le bénévole
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
<script>
    function logout() {
        if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            window.location.href = 'logout.php';
        }
    }
</script>

</html>