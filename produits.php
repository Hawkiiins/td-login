<?php
require "config.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }

$produits = $pdo->query('SELECT * FROM "2026_M1".produit ORDER BY times_in DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gestion du catalogue produits - BackOffice">
    <meta name="author" content="JP">
    <meta name="copyright" content="2026 TD UX/UI">
    <title>Catalogue produits</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <p class="brand">Projet DFS</p>
    <nav aria-label="Navigation principale">
        <a href="profil.php">Mon profil</a>
        <a href="produits.php" aria-current="page">Produits</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>
<main>
<section class="card card-wide" aria-labelledby="titre">
    <h1 id="titre">Catalogue produits</h1>
    <p><a class="btn" href="produit_form.php">+ Ajouter un produit</a></p>
    <?php if (!$produits): ?>
        <p>Aucun produit pour le moment.</p>
    <?php else: ?>
    <table class="tableau">
        <thead>
            <tr>
                <th scope="col">Image</th><th scope="col">Désignation</th><th scope="col">Catégorie</th>
                <th scope="col">Prix HT</th><th scope="col">Promo</th><th scope="col">Stock</th><th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produits as $p): ?>
            <tr>
                <td>
                    <?php if ($p["image_p"]): ?>
                        <img src="uploads/<?= htmlspecialchars($p["image_p"]) ?>" alt="<?= htmlspecialchars($p["designation_p"]) ?>" width="60" height="60" class="miniature">
                    <?php else: ?>
                        <span class="miniature miniature-vide" aria-hidden="true">Pas d'image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p["designation_p"]) ?></td>
                <td><?= htmlspecialchars($p["type_p"]) ?></td>
                <td><?= number_format((float)$p["prix_ht"], 2) ?> €</td>
                <td><?= $p["ppromo"] !== null ? htmlspecialchars($p["ppromo"]) . " %" : "—" ?></td>
                <td><?= (int)$p["stock_p"] ?></td>
                <td>
                    <a href="produit_form.php?id=<?= $p["id_p"] ?>">Modifier</a>
                    &middot;
                    <a href="produit_delete.php?id=<?= $p["id_p"] ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
</main>
<footer class="site-footer"><p>TD UX/UI 2026 : projet fil rouge eCommerce</p></footer>
</body>
</html>