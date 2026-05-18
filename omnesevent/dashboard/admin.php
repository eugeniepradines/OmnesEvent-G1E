<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    if (isset($_POST['approuver'])) {
        $req = $bdd->prepare('UPDATE utilisateurs SET est_approuve=1 WHERE id=?');
        $req->execute(array((int)$_POST['utilisateur_id']));
    }
    if (isset($_POST['rejeter'])) {
        $req = $bdd->prepare('DELETE FROM utilisateurs WHERE id=? AND role="organisateur" AND est_approuve=0');
        $req->execute(array((int)$_POST['utilisateur_id']));
    }
    if (isset($_POST['supprimer_utilisateur'])) {
        $req = $bdd->prepare('DELETE FROM utilisateurs WHERE id=? AND role<>"admin"');
        $req->execute(array((int)$_POST['utilisateur_id']));
    }
    if (isset($_POST['publier_evenement'])) {
        $req = $bdd->prepare('UPDATE evenements SET statut="publie" WHERE id=?');
        $req->execute(array((int)$_POST['evenement_id']));
    }
    if (isset($_POST['annuler_evenement'])) {
        $req = $bdd->prepare('UPDATE evenements SET statut="annule" WHERE id=?');
        $req->execute(array((int)$_POST['evenement_id']));
    }
    header('Location: /omnesevent/dashboard/admin.php');
    exit;
}

$utilisateurs = $bdd->query('SELECT * FROM utilisateurs ORDER BY cree_le DESC')->fetchAll(PDO::FETCH_ASSOC);
$evenements = $bdd->query('SELECT e.*, u.email FROM evenements e INNER JOIN utilisateurs u ON e.cree_par=u.id ORDER BY e.cree_le DESC')->fetchAll(PDO::FETCH_ASSOC);
$demandes = $bdd->query('SELECT * FROM utilisateurs WHERE role="organisateur" AND est_approuve=0')->fetchAll(PDO::FETCH_ASSOC);
$titrePage = 'Administration';
include __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-title"><h1>Administration</h1></div>
    <div class="tabs">
        <button class="tab-btn active" data-tab="users">Utilisateurs</button>
        <button class="tab-btn" data-tab="events">Evenements</button>
        <button class="tab-btn" data-tab="requests">Demandes Organisateurs</button>
    </div>
    <div class="tab-content users panel active">
        <?php foreach ($utilisateurs as $u) { ?>
            <form method="post" class="row confirm-form">
                <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
                <input type="hidden" name="utilisateur_id" value="<?php echo (int)$u['id']; ?>">
                <span><?php echo e($u['prenom'] . ' ' . $u['nom'] . ' - ' . $u['email'] . ' (' . $u['role'] . ')'); ?></span>
                <?php if ($u['role'] !== 'admin') { ?><button class="btn danger" name="supprimer_utilisateur" type="submit">Supprimer</button><?php } ?>
            </form>
        <?php } ?>
    </div>
    <div class="tab-content events panel">
        <?php foreach ($evenements as $event) { ?>
            <form method="post" action="/omnesevent/event/delete.php" class="row confirm-form">
                <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
                <input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
                <input type="hidden" name="evenement_id" value="<?php echo (int)$event['id']; ?>">
                <span><?php echo e($event['titre'] . ' - ' . $event['email'] . ' (' . $event['statut'] . ')'); ?></span>
                <button class="btn ghost" name="publier_evenement" formaction="/omnesevent/dashboard/admin.php" type="submit">Publier</button>
                <button class="btn ghost" name="annuler_evenement" formaction="/omnesevent/dashboard/admin.php" type="submit">Annuler</button>
                <button class="btn danger" type="submit">Supprimer</button>
            </form>
        <?php } ?>
    </div>
    <div class="tab-content requests panel">
        <?php foreach ($demandes as $u) { ?>
            <form method="post" class="row">
                <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
                <input type="hidden" name="utilisateur_id" value="<?php echo (int)$u['id']; ?>">
                <span><?php echo e($u['prenom'] . ' ' . $u['nom'] . ' - ' . $u['email']); ?></span>
                <button class="btn" name="approuver" type="submit">Approuver</button>
                <button class="btn danger" name="rejeter" type="submit">Rejeter</button>
            </form>
        <?php } ?>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
