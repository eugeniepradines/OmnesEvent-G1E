<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('participant');

$requete = $bdd->prepare('SELECT i.*, e.titre, e.date_evenement, e.nom_lieu FROM inscriptions i INNER JOIN evenements e ON i.evenement_id=e.id WHERE i.utilisateur_id=? AND i.statut <> "annule" ORDER BY e.date_evenement ASC');
$requete->execute(array($_SESSION['utilisateur_id']));
$billets = $requete->fetchAll(PDO::FETCH_ASSOC);
$titrePage = 'Mes billets';
include __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-title"><h1>Mes billets</h1></div>
    <div class="tabs">
        <button class="tab-btn active" data-tab="futur">A venir</button>
        <button class="tab-btn" data-tab="passe">Passes</button>
    </div>
    <div class="tickets">
        <?php foreach ($billets as $billet) {
            $passe = strtotime($billet['date_evenement']) < time();
        ?>
            <article class="ticket panel tab-content <?php echo $passe ? 'passe' : 'futur'; ?>">
                <div>
                    <h2><?php echo e($billet['titre']); ?></h2>
                    <p><?php echo date('d/m/Y H:i', strtotime($billet['date_evenement'])); ?> - <?php echo e($billet['nom_lieu']); ?></p>
                    <span class="badge <?php echo e($billet['statut']); ?>"><?php echo e($billet['statut']); ?></span>
                </div>
                <img class="qr" src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?php echo e($billet['token_qr']); ?>" alt="QR code">
            </article>
        <?php } ?>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
