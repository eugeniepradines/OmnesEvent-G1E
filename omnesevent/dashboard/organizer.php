<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('organisateur');

$requete = $bdd->prepare('SELECT e.*, COUNT(i.id) AS total_inscriptions FROM evenements e LEFT JOIN inscriptions i ON i.evenement_id=e.id AND i.statut<>"annule" WHERE e.cree_par=? GROUP BY e.id ORDER BY e.date_evenement DESC');
$requete->execute(array($_SESSION['utilisateur_id']));
$evenements = $requete->fetchAll(PDO::FETCH_ASSOC);

$inscrits = array();
if (isset($_GET['event'])) {
    $req = $bdd->prepare('SELECT i.*, u.prenom, u.nom, u.email FROM inscriptions i INNER JOIN utilisateurs u ON i.utilisateur_id=u.id WHERE i.evenement_id=? ORDER BY i.inscrit_le ASC');
    $req->execute(array((int)$_GET['event']));
    $inscrits = $req->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['presence_id'])) {
    verifierCSRF();
    $maj = $bdd->prepare('UPDATE inscriptions SET presente=? WHERE id=?');
    $maj->execute(array(isset($_POST['presente']) ? 1 : 0, (int)$_POST['presence_id']));
    header('Location: /omnesevent/dashboard/organizer.php?event=' . (int)$_POST['event_id']);
    exit;
}

$titrePage = 'Dashboard organisateur';
include __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-title">
        <h1>Tableau de bord organisateur</h1>
        <a class="btn" href="/omnesevent/event/create.php">Creer un evenement</a>
    </div>
    <div class="dashboard-grid">
        <?php foreach ($evenements as $event) {
            $taux = $event['capacite'] > 0 ? min(100, round($event['total_inscriptions'] * 100 / $event['capacite'])) : 0;
        ?>
            <article class="panel">
                <span class="badge"><?php echo e($event['statut']); ?></span>
                <h2><?php echo e($event['titre']); ?></h2>
                <p><?php echo (int)$event['total_inscriptions']; ?> inscription(s)</p>
                <div class="progress"><span style="width: <?php echo $taux; ?>%"></span></div>
                <div class="actions">
                    <a class="btn ghost" href="/omnesevent/dashboard/organizer.php?event=<?php echo (int)$event['id']; ?>">Inscrits</a>
                    <a class="btn ghost" href="/omnesevent/event/edit.php?id=<?php echo (int)$event['id']; ?>">Modifier</a>
                    <form method="post" action="/omnesevent/event/delete.php" class="inline-form confirm-form">
                        <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
                        <input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
                        <button class="btn danger" type="submit">Supprimer</button>
                    </form>
                </div>
            </article>
        <?php } ?>
    </div>
    <?php if ($inscrits) { ?>
        <div class="panel">
            <h2>Liste des inscrits</h2>
            <?php foreach ($inscrits as $inscrit) { ?>
                <form method="post" class="row">
                    <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
                    <input type="hidden" name="presence_id" value="<?php echo (int)$inscrit['id']; ?>">
                    <input type="hidden" name="event_id" value="<?php echo (int)$_GET['event']; ?>">
                    <span><?php echo e($inscrit['prenom'] . ' ' . $inscrit['nom'] . ' - ' . $inscrit['email']); ?></span>
                    <label class="switch">Present <input type="checkbox" name="presente" onchange="this.form.submit()" <?php if ($inscrit['presente']) echo 'checked'; ?>></label>
                </form>
            <?php } ?>
        </div>
    <?php } ?>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
