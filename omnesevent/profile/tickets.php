<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('participant');

$requete = $bdd->prepare('SELECT i.*, e.titre, e.date_evenement, e.nom_lieu FROM inscriptions i INNER JOIN evenements e ON i.evenement_id=e.id WHERE i.utilisateur_id=? ORDER BY e.date_evenement ASC');
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
                    <span class="badge status-badge <?php echo e($billet['statut']); ?>"><?php echo e($billet['statut']); ?></span>
                    <?php if ($billet['statut'] === 'liste_attente') { ?><p class="wait-note">Tu seras automatiquement inscrit(e) si une place se libere.</p><?php } ?>
                </div>
                <button class="qr-open" type="button" aria-label="Agrandir le QR code" data-qr-src="https://api.qrserver.com/v1/create-qr-code/?size=700x700&data=<?php echo e($billet['token_qr']); ?>">
                    <img class="qr" src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?php echo e($billet['token_qr']); ?>" alt="QR code">
                </button>
            </article>
        <?php } ?>
    </div>
</section>
<div class="qr-modal" aria-hidden="true">
    <button class="qr-modal-close" type="button" aria-label="Fermer">x</button>
    <div class="qr-modal-card">
        <img src="" alt="QR code agrandi">
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.querySelector('.qr-modal');
    if (!modal) return;
    var image = modal.querySelector('img');
    document.querySelectorAll('.qr-open').forEach(function (button) {
        button.addEventListener('click', function () {
            image.src = button.dataset.qrSrc || button.querySelector('img').src.replace('size=140x140', 'size=700x700');
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        });
    });
    function fermerQr() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }
    modal.addEventListener('click', function (event) {
        if (event.target === modal || event.target.classList.contains('qr-modal-close')) {
            fermerQr();
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') fermerQr();
    });
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
