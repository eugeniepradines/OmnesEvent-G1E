<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$requete = $bdd->prepare('SELECT e.*, u.prenom, u.nom FROM evenements e INNER JOIN utilisateurs u ON e.cree_par = u.id WHERE e.id = ?');
$requete->execute(array($id));
$evenement = $requete->fetch(PDO::FETCH_ASSOC);
if (!$evenement) {
    die('Evenement introuvable.');
}

$inscription = null;
if (estConnecte()) {
    $req = $bdd->prepare('SELECT * FROM inscriptions WHERE evenement_id = ? AND utilisateur_id = ? AND statut <> "annule"');
    $req->execute(array($id, $_SESSION['utilisateur_id']));
    $inscription = $req->fetch(PDO::FETCH_ASSOC);
}
$places = placesRestantes($bdd, $evenement);
$affiche = urlSite($evenement['url_affiche'] ?: '/assets/img/default-event.svg');
$prixClasse = evenementGratuit($evenement) ? 'free' : 'paid';
$aCoordonnees = $evenement['latitude'] !== null && $evenement['longitude'] !== null;
$titrePage = $evenement['titre'];
$styles = $aCoordonnees ? array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css') : array();
include __DIR__ . '/../includes/header.php';
?>
<section class="detail-hero" style="background-image: linear-gradient(180deg, rgba(13,15,20,.25), #0d0f14), url('<?php echo e($affiche); ?>');">
    <div>
        <span class="badge"><?php echo e($evenement['categorie']); ?></span>
        <h1><?php echo e($evenement['titre']); ?></h1>
    </div>
</section>
<section class="detail-layout">
    <article class="panel">
        <h2>Details</h2>
        <p><?php echo nl2br(e($evenement['description'])); ?></p>
        <div class="info-grid">
            <p><strong>Date</strong><?php echo date('d/m/Y H:i', strtotime($evenement['date_evenement'])); ?></p>
            <p><strong>Lieu</strong><?php echo e($evenement['nom_lieu']); ?><br><?php echo e($evenement['adresse_lieu']); ?></p>
            <p><strong>Organisateur</strong><?php echo e($evenement['prenom'] . ' ' . $evenement['nom']); ?></p>
            <p><strong>Places restantes</strong><span id="places-restantes"><?php echo $places; ?></span></p>
        </div>
        <?php if ($aCoordonnees) { ?>
            <div id="event-map" class="event-map" data-lat="<?php echo e($evenement['latitude']); ?>" data-lng="<?php echo e($evenement['longitude']); ?>" data-title="<?php echo e($evenement['nom_lieu']); ?>" data-address="<?php echo e($evenement['adresse_lieu']); ?>"></div>
        <?php } ?>
    </article>
    <aside class="panel reserve-panel">
        <span class="badge price-detail <?php echo e($prixClasse); ?>"><?php echo e(libellePrix($evenement)); ?></span>
        <?php if (!estConnecte()) { ?>
            <a class="btn full" href="<?php echo e(urlSite('/login.php')); ?>">Connectez-vous pour reserver</a>
        <?php } else { ?>
            <button class="btn full reserve-btn" data-event="<?php echo (int)$evenement['id']; ?>" data-token="<?php echo e(jetonCSRF()); ?>" data-reserve-label="<?php echo e(libelleReservation($evenement)); ?>" data-wait-label="Rejoindre la liste d attente">
                <?php
                if ($inscription) echo $inscription['statut'] === 'liste_attente' ? 'Annuler ma liste d attente' : 'Annuler ma reservation';
                elseif ($places <= 0) echo 'Rejoindre la liste d attente';
                else echo e(libelleReservation($evenement));
                ?>
            </button>
            <p id="reserve-message" class="muted"></p>
        <?php } ?>
    </aside>
</section>
<?php
$scripts = $aCoordonnees
    ? array('/assets/js/reserve.js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', '/assets/js/event-detail.js')
    : array('/assets/js/reserve.js');
include __DIR__ . '/../includes/footer.php';
?>
