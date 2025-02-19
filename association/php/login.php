<?php
session_start();
require 'config.php';
require 'theme.php';

$error = ''; // Initialiser la variable pour le message d'erreur

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM benevoles WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["nom"] = $user["nom"];
        $_SESSION["role"] = $user["role"];

        if (!headers_sent()) {
            header("Location: collection_list.php");
            exit;
        } else {
            echo "<script>window.location.href='collection_list.php';</script>";
            exit;
        }
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="flex justify-center items-center min-h-screen">
        <div class="<?= $theme['tableBg'] ?> p-8 w-full sm:w-96">
            <h1 class="text-3xl font-bold <?= $theme['textColor'] ?> mb-6 text-center">Connexion</h1>

            <?php if (!empty($error)) : ?>
                <div class="text-red-600 text-center mb-4 text-lg font-semibold">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium <?= $theme['textColor'] ?>">Email</label>
                    <input type="email" name="email" id="email" required class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium <?= $theme['textColor'] ?>">Mot de passe</label>
                    <input type="password" name="password" id="password" required class="w-full p-3 border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-between items-center">
                    <a href="#" class="text-sm text-blue-600 hover:underline">Mot de passe oublié ?</a>
                    <button type="submit" class="<?= $theme['buttons'] ?>">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
