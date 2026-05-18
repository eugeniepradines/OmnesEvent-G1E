<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$categorie = $_GET['categorie'] ?? '';
$recherche = $_GET['q'] ?? '';
$date = $_GET['date'] ?? '';
$association = (int)($_GET['association'] ?? 0);

$sql = "SELECT e.*, u.prenom, u.nom,
        (e.capacite - (
            SELECT COUNT(*) FROM inscriptions i
            WHERE i.evenement_id = e.id AND i.statut = 'confirme'
        )) AS places_restantes
        FROM evenements e
        INNER JOIN utilisateurs u ON e.cree_par = u.id
        WHERE e.statut = 'publie'";
$params = array();

if ($categorie !== '') {
    $sql .= ' AND e.categorie = ?';
    $params[] = $categorie;
}
if ($recherche !== '') {
    $sql .= ' AND (e.titre LIKE ? OR e.description LIKE ? OR e.nom_lieu LIKE ?)';
    $params[] = '%' . $recherche . '%';
    $params[] = '%' . $recherche . '%';
    $params[] = '%' . $recherche . '%';
}
if ($date !== '') {
    $sql .= ' AND DATE(e.date_evenement) = ?';
    $params[] = $date;
}
if ($association > 0) {
    $sql .= ' AND EXISTS (SELECT 1 FROM associations a WHERE a.organisateur_id = e.cree_par AND a.id = ?)';
    $params[] = $association;
}
$sql .= ' ORDER BY e.date_evenement ASC';
$requete = $bdd->prepare($sql);
$requete->execute($params);
$evenements = $requete->fetchAll(PDO::FETCH_ASSOC);
$requeteAssociations = $bdd->query('SELECT id, nom FROM associations ORDER BY nom ASC');
$associations = $requeteAssociations->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['ajax'])) {
    foreach ($evenements as $evenement) {
        include __DIR__ . '/includes/event-card.php';
    }
    exit;
}

$titrePage = 'OmnesEvent - Accueil';
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <canvas id="particles"></canvas>
    <div class="hero-content">
        <p class="eyebrow">Billetterie et vie de campus</p>
        <h1>Tous les événements Omnes, au même endroit.</h1>
        <form class="filters ajax-filters" method="get" action="/omnesevent/index.php">
            <input type="search" name="q" placeholder="Rechercher un evenement" value="<?php echo e($recherche); ?>">
            <select name="categorie">
                <option value="">Categorie</option>
                <?php foreach (array('Soirée','Sport','Culture','Conférence','Autre') as $cat) { ?>
                    <option value="<?php echo e($cat); ?>" <?php if ($categorie === $cat) echo 'selected'; ?>><?php echo e($cat); ?></option>
                <?php } ?>
            </select>
            <input type="date" name="date" value="<?php echo e($date); ?>">
            <select name="association">
                <option value="">Association</option>
                <?php foreach ($associations as $asso) { ?>
                    <option value="<?php echo (int)$asso['id']; ?>" <?php if ($association === (int)$asso['id']) echo 'selected'; ?>><?php echo e($asso['nom']); ?></option>
                <?php } ?>
            </select>
            <button class="btn" type="submit">Filtrer</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="section-title">
        <p class="eyebrow">Selection</p>
        <h2>Evenements a venir</h2>
    </div>
    <div id="event-grid" class="event-grid">
        <?php foreach ($evenements as $evenement) { include __DIR__ . '/includes/event-card.php'; } ?>
    </div>
</section>
<?php
$scripts = array('/omnesevent/assets/js/particles.js');
include __DIR__ . '/includes/footer.php';
?>
