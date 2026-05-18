<?php
require_once __DIR__ . '/auth.php';
$titrePage = $titrePage ?? 'OmnesEvent';
$utilisateurNav = null;
if (isset($bdd)) {
    $utilisateurNav = getUtilisateur($bdd);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titrePage); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/omnesevent/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="/omnesevent/index.php">OmnesEvent</a>
        <button class="menu-toggle" type="button" aria-label="Menu">☰</button>
        <div class="nav-links">
            <a href="/omnesevent/index.php">Accueil</a>
            <a href="/omnesevent/event/list.php">Evenements</a>
            <?php if (estConnecte() && $_SESSION['role'] === 'participant') { ?>
                <a href="/omnesevent/profile/tickets.php">Mes billets</a>
            <?php } ?>
            <?php if (estConnecte() && $_SESSION['role'] === 'organisateur') { ?>
                <a href="/omnesevent/dashboard/organizer.php">Organisateur</a>
            <?php } ?>
            <?php if (estConnecte() && $_SESSION['role'] === 'admin') { ?>
                <a href="/omnesevent/dashboard/admin.php">Admin</a>
            <?php } ?>
        </div>
        <div class="nav-actions">
            <?php if (estConnecte()) { ?>
                <a class="btn ghost" href="/omnesevent/profile/index.php"><?php echo e($_SESSION['prenom']); ?></a>
                <a class="btn small" href="/omnesevent/logout.php">Deconnexion</a>
            <?php } else { ?>
                <a class="btn ghost" href="/omnesevent/login.php">Connexion</a>
                <a class="btn small" href="/omnesevent/register.php">Inscription</a>
            <?php } ?>
        </div>
    </nav>
</header>
<main>
