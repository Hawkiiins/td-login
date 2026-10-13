<?php
require "config.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }

$stmt = $pdo->prepare('SELECT user_mail, user_date_new FROM "2026_M1"."user" WHERE user_id = :id');
$stmt->execute(["id" => $_SESSION["user_id"]]);
$user = $stmt->fetch();
if (!$user) { header("Location: logout.php"); exit; }
$membreDepuis = date("d/m/Y", strtotime($user["user_date_new"]));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Votre profil">
    <meta name="author" content="JP">
    <meta name="copyright" content="2026 TD UX/UI">
    <title>Mon profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a class="skip" href="#contenu">Aller au contenu</a>
    <header class="site-header">
        <p class="brand">Projet DFS</p>
        <nav aria-label="Navigation principale">
            <a href="profil.php" aria-current="page">Mon profil</a>
            <a href="produits.php">Produits</a>
            <a href="logout.php">Se déconnecter</a>
        </nav>
    </header>
    <main id="contenu">
        <section class="card" aria-labelledby="titre">
            <h1 id="titre">Bienvenue <?= htmlspecialchars($_SESSION["user_login"]) ?></h1>
            <dl class="infos">
                <dt>Adresse mail</dt><dd><?= htmlspecialchars($user["user_mail"]) ?></dd>
                <dt>Membre depuis</dt><dd><?= htmlspecialchars($membreDepuis) ?></dd>
            </dl>
        </section>
    </main>
    <footer class="site-footer"><p>TD UX/UI 2026 : projet fil rouge eCommerce</p></footer>
</body>
</html>