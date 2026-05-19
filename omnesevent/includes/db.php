<?php
require_once __DIR__ . '/../config.php';

try {
    $port = defined('DB_PORT') ? DB_PORT : 3306;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
