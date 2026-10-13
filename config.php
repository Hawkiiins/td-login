<?php
session_start();
$dsn = "pgsql:host=host.docker.internal;port=5430;dbname=td_ux";
$pdo = new PDO($dsn, "postgres", "postgres", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);