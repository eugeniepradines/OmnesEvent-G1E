<?php
require_once __DIR__ . '/auth.php';
$titrePage = $titrePage ?? 'OmnesEvent';
$assetVersion = '20260519c';
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
    <?php if (!empty($styles)) { foreach ($styles as $style) { ?>
        <link rel="stylesheet" href="<?php echo e(urlSite($style)); ?>">
    <?php } } ?>
    <link rel="stylesheet" href="<?php echo e(urlSite('/assets/css/style.css') . '?v=' . $assetVersion); ?>">
</head>
<body data-base-url="<?php echo e(baseUrlSite()); ?>">
<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="<?php echo e(urlSite('/index.php')); ?>">OmnesEvent</a>
        <button class="menu-toggle" type="button" aria-label="Menu">☰</button>
        <div class="nav-links">
            <a href="<?php echo e(urlSite('/index.php')); ?>">Accueil</a>
            <a href="<?php echo e(urlSite('/event/list.php')); ?>">Evenements</a>
            <?php if (estConnecte() && $_SESSION['role'] === 'participant') { ?>
                <a href="<?php echo e(urlSite('/profile/tickets.php')); ?>">Mes billets</a>
            <?php } ?>
            <?php if (estConnecte() && $_SESSION['role'] === 'organisateur') { ?>
                <a href="<?php echo e(urlSite('/dashboard/organizer.php')); ?>">Organisateur</a>
            <?php } ?>
            <?php if (estConnecte() && $_SESSION['role'] === 'admin') { ?>
                <a href="<?php echo e(urlSite('/dashboard/admin.php')); ?>">Admin</a>
            <?php } ?>
        </div>
        <div class="nav-actions">
            <?php if (estConnecte()) { ?>
                <a class="btn ghost" href="<?php echo e(urlSite('/profile/index.php')); ?>"><?php echo e($_SESSION['prenom']); ?></a>
                <a class="btn small" href="<?php echo e(urlSite('/logout.php')); ?>">Deconnexion</a>
            <?php } else { ?>
                <a class="btn ghost" href="<?php echo e(urlSite('/login.php')); ?>">Connexion</a>
                <a class="btn small" href="<?php echo e(urlSite('/register.php')); ?>">Inscription</a>
            <?php } ?>
        </div>
    </nav>
</header>
<main>
