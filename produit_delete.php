<?php
require "config.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }

$id = $_GET["id"] ?? null;
if ($id) {
    $stmt = $pdo->prepare('SELECT image_p FROM "2026_M1".produit WHERE id_p = :id');
    $stmt->execute(["id" => $id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists(__DIR__ . "/uploads/" . $img)) { unlink(__DIR__ . "/uploads/" . $img); }
    $stmt = $pdo->prepare('DELETE FROM "2026_M1".produit WHERE id_p = :id');
    $stmt->execute(["id" => $id]);
}
header("Location: produits.php");
exit;