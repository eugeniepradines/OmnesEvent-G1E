<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $requete = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $requete->execute(array($email));
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe_hash'])) {
        if ($utilisateur['role'] === 'organisateur' && (int)$utilisateur['est_approuve'] === 0) {
            $erreur = 'Votre compte organisateur attend une validation admin.';
        } else {
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['role'] = $utilisateur['role'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            if ($utilisateur['role'] === 'admin') rediriger('/dashboard/admin.php');
            elseif ($utilisateur['role'] === 'organisateur') rediriger('/dashboard/organizer.php');
            else rediriger('/index.php');
        }
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
$titrePage = 'Connexion';
include __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <form class="auth-card validate-form" method="post">
        <h1>Connexion</h1>
        <?php if ($erreur) { ?><p class="alert"><?php echo e($erreur); ?></p><?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
        <label>Email<input type="email" name="email" required></label>
        <label>Mot de passe<input type="password" name="mot_de_passe" required minlength="6"></label>
        <button class="btn full" type="submit">Se connecter</button>
        <p class="muted">Pas encore de compte ? <a href="<?php echo e(urlSite('/register.php')); ?>">Inscription</a></p>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
