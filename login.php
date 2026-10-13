<?php
require "config.php";

$erreur = ""; $login = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $mdp   = $_POST["password"] ?? "";

    $stmt = $pdo->prepare('SELECT user_id, user_login, user_password FROM "2026_M1"."user" WHERE user_login = :login');
    $stmt->execute(["login" => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user["user_password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["user_login"] = $user["user_login"];
        $upd = $pdo->prepare('UPDATE "2026_M1"."user" SET user_date_login = NOW() WHERE user_id = :id');
        $upd->execute(["id" => $user["user_id"]]);
        header("Location: profil.php");
        exit;
    }
    $erreur = "Identifiants incorrects.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Connexion à votre compte">
    <meta name="author" content="JP">
    <meta name="copyright" content="2026 TD UX/UI">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <a class="skip" href="#contenu">Aller au contenu</a>
    <header class="site-header">
        <p class="brand">Projet DFS </p>
        <nav aria-label="Navigation principale">
            <a href="login.php" aria-current="page">Connexion</a>
            <a href="register.php">Inscription</a>
        </nav>
    </header>
    <main id="contenu">
        <section class="card" aria-labelledby="titre">
            <h1 id="titre">Connexion</h1>
            <?php if ($erreur): ?><div class="toast toast-error" role="alert"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
            <form method="post" data-loader>
                <div class="field">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required autocomplete="username" value="<?= htmlspecialchars($login) ?>">
                </div>
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn">Se connecter</button>
                <p id="statut" class="sr-only" role="status"></p>
            </form>
            <p class="card-links">Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
        </section>
    </main>
    <footer class="site-footer"><p>TD UX/UI 2026 : projet fil rouge eCommerce</p></footer>
</body>
</html>