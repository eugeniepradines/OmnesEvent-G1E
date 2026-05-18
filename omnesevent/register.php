<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$message = '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] === 'organisateur' ? 'organisateur' : 'participant';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    if ($prenom && $nom && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($mot_de_passe) >= 6) {
        try {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $approuve = $role === 'participant' ? 1 : 0;
            $requete = $bdd->prepare('INSERT INTO utilisateurs(email, mot_de_passe_hash, prenom, nom, role, est_approuve, cree_le) VALUES(?,?,?,?,?,?,NOW())');
            $requete->execute(array($email, $hash, $prenom, $nom, $role, $approuve));
            $message = $role === 'organisateur' ? 'Compte cree. Un admin doit valider votre acces organisateur.' : 'Compte cree. Vous pouvez vous connecter.';
        } catch (Exception $e) {
            $erreur = 'Cet email existe deja.';
        }
    } else {
        $erreur = 'Veuillez remplir correctement tous les champs.';
    }
}
$titrePage = 'Inscription';
include __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <form class="auth-card validate-form" method="post">
        <h1>Inscription</h1>
        <?php if ($message) { ?><p class="success"><?php echo e($message); ?></p><?php } ?>
        <?php if ($erreur) { ?><p class="alert"><?php echo e($erreur); ?></p><?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
        <div class="two-cols">
            <label>Prenom<input type="text" name="prenom" required></label>
            <label>Nom<input type="text" name="nom" required></label>
        </div>
        <label>Email<input type="email" name="email" required></label>
        <label>Mot de passe<input type="password" name="mot_de_passe" required minlength="6"></label>
        <label>Role
            <select name="role" id="role-register">
                <option value="participant">Participant</option>
                <option value="organisateur">Organisateur</option>
            </select>
        </label>
        <p class="notice">Les comptes Organisateur necessitent une validation admin.</p>
        <button class="btn full" type="submit">Creer mon compte</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
