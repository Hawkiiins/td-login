<?php
require "config.php";
$n = $pdo->query('SELECT COUNT(*) FROM "2026_M1"."user"')->fetchColumn();
echo "Connexion OK, $n utilisateur(s).";