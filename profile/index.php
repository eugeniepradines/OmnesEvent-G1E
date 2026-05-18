<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();
$utilisateur = getUtilisateur($bdd);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    $requete = $bdd->prepare('UPDATE utilisateurs SET prenom=?, nom=?, avatar_url=? WHERE id=?');
    $requete->execute(array($_POST['prenom'], $_POST['nom'], $_POST['avatar_url'], $_SESSION['utilisateur_id']));
    $_SESSION['prenom'] = $_POST['prenom'];
    $message = 'Profil mis a jour.';
    $utilisateur = getUtilisateur($bdd);
}
$titrePage = 'Mon profil';
include __DIR__ . '/../includes/header.php';
?>
<section class="form-page">
    <form class="panel auth-card" method="post">
        <h1>Mon profil</h1>
        <?php if ($message) { ?><p class="success"><?php echo e($message); ?></p><?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
        <label>Prenom<input type="text" name="prenom" required value="<?php echo e($utilisateur['prenom']); ?>"></label>
        <label>Nom<input type="text" name="nom" required value="<?php echo e($utilisateur['nom']); ?>"></label>
        <label>Avatar URL<input type="url" name="avatar_url" value="<?php echo e($utilisateur['avatar_url']); ?>"></label>
        <p class="muted">Role : <?php echo e($utilisateur['role']); ?></p>
        <button class="btn full" type="submit">Enregistrer</button>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
