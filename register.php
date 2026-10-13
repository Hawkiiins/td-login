<?php
require "config.php";

$erreur = ""; $succes = ""; $login = ""; $mail = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $mail  = trim($_POST["mail"] ?? "");
    $mdp   = $_POST["password"] ?? "";

    if ($login === "" || $mail === "" || $mdp === "") {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!preg_match('/^[A-Za-z0-9_-]{3,30}$/D', $login)) {
        $erreur = "Login : 3 à 30 caractères (lettres, chiffres, _ ou -).";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse mail invalide.";
    } elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/D', $mdp)) {
        $erreur = "Mot de passe : 8 caractères min., 1 majuscule, 1 minuscule, 1 chiffre.";
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM "2026_M1"."user" WHERE user_login = :login OR user_mail = :mail');
        $stmt->execute(["login" => $login, "mail" => $mail]);
        if ($stmt->fetch()) {
            $erreur = "Login ou mail déjà utilisé.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO "2026_M1"."user" (user_login, user_password, user_compte_id, user_mail) VALUES (:login, :hash, (SELECT COALESCE(MAX(user_compte_id), 0) + 1 FROM "2026_M1"."user"), :mail)');
            $stmt->execute(["login" => $login, "hash" => $hash, "mail" => $mail]);
            $succes = 'Compte créé. <a href="login.php">Se connecter</a>';
            $login = $mail = "";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Création de compte">
    <meta name="author" content="JP">
    <meta name="copyright" content="2026 TD UX/UI">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <a class="skip" href="#contenu">Aller au contenu</a>
    <header class="site-header">
        <p class="brand">Projet DFS</p>
        <nav aria-label="Navigation principale">
            <a href="login.php">Connexion</a>
            <a href="register.php" aria-current="page">Inscription</a>
        </nav>
    </header>
    <main id="contenu">
        <section class="card" aria-labelledby="titre">
            <h1 id="titre">Créer un compte</h1>
            <?php if ($erreur): ?><div class="toast toast-error" role="alert"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="toast toast-success" role="status"><?= $succes ?></div><?php endif; ?>
            <form method="post" data-loader>
                <div class="field">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required pattern="[A-Za-z0-9_\-]{3,30}" title="3 à 30 caractères : lettres, chiffres, _ ou -" autocomplete="username" aria-describedby="aide-login" value="<?= htmlspecialchars($login) ?>">
                    <p class="hint" id="aide-login">3 à 30 caractères : lettres, chiffres, _ ou -</p>
                </div>
                <div class="field">
                    <label for="mail">Adresse mail</label>
                    <input type="email" id="mail" name="mail" required placeholder="prenom.nom@mail.fr" autocomplete="email" aria-describedby="aide-mail" value="<?= htmlspecialchars($mail) ?>">
                    <p class="hint" id="aide-mail">Exemple : prenom.nom@mail.fr</p>
                </div>
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="8 caractères min. : 1 majuscule, 1 minuscule, 1 chiffre" autocomplete="new-password" aria-describedby="aide-mdp">
                    <p class="hint" id="aide-mdp">8 caractères min. : 1 majuscule, 1 minuscule, 1 chiffre.</p>
                </div>
                <button type="submit" class="btn">Créer mon compte</button>
                <p id="statut" class="sr-only" role="status"></p>
            </form>
            <p class="card-links">Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </section>
    </main>
    <footer class="site-footer"><p>TD UX/UI 2026 : projet fil rouge eCommerce</p></footer>
</body>
</html>