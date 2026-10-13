<?php
require "config.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }

$categories = json_decode(file_get_contents(__DIR__ . "/data/categories.json"), true);
$id = $_GET["id"] ?? $_POST["id"] ?? null;
$produit = ["id_p" => "", "type_p" => "", "designation_p" => "", "prix_ht" => "", "stock_p" => 0, "ppromo" => "", "image_p" => ""];
$erreur = "";

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM "2026_M1".produit WHERE id_p = :id');
    $stmt->execute(["id" => $id]);
    $existant = $stmt->fetch();
    if ($existant) { $produit = $existant; }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $produit["type_p"] = trim($_POST["type_p"] ?? "");
    $produit["designation_p"] = trim($_POST["designation_p"] ?? "");
    $produit["prix_ht"] = $_POST["prix_ht"] ?? "";
    $produit["stock_p"] = $_POST["stock_p"] ?? 0;
    $produit["ppromo"] = $_POST["ppromo"] !== "" ? $_POST["ppromo"] : null;

    if ($produit["designation_p"] === "" || $produit["type_p"] === "" || $produit["prix_ht"] === "") {
        $erreur = "Désignation, catégorie et prix sont obligatoires.";
    } elseif (!is_numeric($produit["prix_ht"]) || $produit["prix_ht"] < 0) {
        $erreur = "Le prix doit être un nombre positif.";
    } else {
        $nomImage = $produit["image_p"];
        if (!empty($_FILES["image"]["name"])) {
            $extensionsOk = ["jpg", "jpeg", "png", "webp"];
            $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensionsOk, true)) {
                $erreur = "Image : formats acceptés jpg, png, webp.";
            } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
                $erreur = "Image : 2 Mo maximum.";
            } else {
                $nomImage = uniqid("prod_") . "." . $ext;
                move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . "/uploads/" . $nomImage);
            }
        }
        if (!$erreur) {
            $produit["image_p"] = $nomImage;
            if ($id) {
                $stmt = $pdo->prepare('UPDATE "2026_M1".produit SET type_p = :type, designation_p = :desig, prix_ht = :prix, stock_p = :stock, ppromo = :promo, image_p = :image WHERE id_p = :id');
                $stmt->execute(["type" => $produit["type_p"], "desig" => $produit["designation_p"], "prix" => $produit["prix_ht"], "stock" => $produit["stock_p"], "promo" => $produit["ppromo"], "image" => $produit["image_p"], "id" => $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO "2026_M1".produit (type_p, designation_p, prix_ht, date_in, stock_p, ppromo, image_p) VALUES (:type, :desig, :prix, CURRENT_DATE, :stock, :promo, :image)');
                $stmt->execute(["type" => $produit["type_p"], "desig" => $produit["designation_p"], "prix" => $produit["prix_ht"], "stock" => $produit["stock_p"], "promo" => $produit["ppromo"], "image" => $produit["image_p"]]);
            }
            header("Location: produits.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ajout ou modification d'un produit">
    <meta name="author" content="JP">
    <meta name="copyright" content="2026 TD UX/UI">
    <title><?= $id ? "Modifier" : "Ajouter" ?> un produit</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
<header class="site-header">
    <p class="brand">Projet DFS</p>
    <nav aria-label="Navigation principale">
        <a href="profil.php">Mon profil</a>
        <a href="produits.php">Produits</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>
<main>
<section class="card" aria-labelledby="titre">
    <h1 id="titre"><?= $id ? "Modifier" : "Ajouter" ?> un produit</h1>
    <?php if ($erreur): ?><div class="toast toast-error" role="alert"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" data-loader>
        <?php if ($id): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
        <div class="field">
            <label for="designation_p">Désignation</label>
            <input type="text" id="designation_p" name="designation_p" required placeholder="Ex : iPhone 15" value="<?= htmlspecialchars($produit["designation_p"]) ?>">
        </div>
        <div class="field">
            <label for="type_p">Catégorie</label>
            <select id="type_p" name="type_p" required>
                <option value="">— Choisir —</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat["categorie"]) ?>" <?= $produit["type_p"] === $cat["categorie"] ? "selected" : "" ?>><?= htmlspecialchars($cat["categorie"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="prix_ht">Prix HT (€)</label>
            <input type="number" id="prix_ht" name="prix_ht" required min="0" step="0.01" value="<?= htmlspecialchars((string)$produit["prix_ht"]) ?>">
        </div>
        <div class="field">
            <label for="stock_p">Stock</label>
            <input type="number" id="stock_p" name="stock_p" min="0" step="1" value="<?= htmlspecialchars((string)$produit["stock_p"]) ?>">
        </div>
        <div class="field">
            <label for="ppromo">Promotion (%)</label>
            <input type="number" id="ppromo" name="ppromo" min="0" max="100" step="1" placeholder="Laisser vide si aucune" value="<?= htmlspecialchars((string)$produit["ppromo"]) ?>">
            <p class="hint">Vide = prix HT affiché tel quel.</p>
        </div>
        <div class="field">
            <label for="image">Image (jpg, png ou webp, 2 Mo max)</label>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
            <?php if ($produit["image_p"]): ?><p class="hint">Image actuelle : <?= htmlspecialchars($produit["image_p"]) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="btn"><?= $id ? "Enregistrer" : "Ajouter" ?></button>
        <p id="statut" class="sr-only" role="status"></p>
    </form>
    <p class="card-links"><a href="produits.php">&larr; Retour au catalogue</a></p>
</section>
</main>
<footer class="site-footer"><p>TD UX/UI 2026 : projet fil rouge eCommerce</p></footer>
</body>
</html>