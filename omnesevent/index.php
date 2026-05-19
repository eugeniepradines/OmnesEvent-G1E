<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$sqlProchain = "SELECT e.*, u.prenom, u.nom,
        (e.capacite - (
            SELECT COUNT(*) FROM inscriptions i
            WHERE i.evenement_id = e.id AND i.statut = 'confirme'
        )) AS places_restantes
        FROM evenements e
        INNER JOIN utilisateurs u ON e.cree_par = u.id
        WHERE e.statut = ? AND e.date_evenement > NOW()
        ORDER BY e.date_evenement ASC
        LIMIT 1";
$requete = $bdd->prepare($sqlProchain);
$requete->execute(array('publie'));
$prochainEvenement = $requete->fetch(PDO::FETCH_ASSOC);

$sqlApercu = "SELECT e.*, u.prenom, u.nom,
        (e.capacite - (
            SELECT COUNT(*) FROM inscriptions i
            WHERE i.evenement_id = e.id AND i.statut = 'confirme'
        )) AS places_restantes
        FROM evenements e
        INNER JOIN utilisateurs u ON e.cree_par = u.id
        WHERE e.statut = ? AND e.date_evenement > NOW()";
$paramsApercu = array('publie');
if ($prochainEvenement) {
    $sqlApercu .= ' AND e.id <> ?';
    $paramsApercu[] = $prochainEvenement['id'];
}
$sqlApercu .= ' ORDER BY e.date_evenement ASC LIMIT 3';
$requete = $bdd->prepare($sqlApercu);
$requete->execute($paramsApercu);
$evenementsApercu = $requete->fetchAll(PDO::FETCH_ASSOC);

$requete = $bdd->prepare("SELECT COUNT(*) FROM evenements WHERE statut = ?");
$requete->execute(array('publie'));
$totalEvenements = (int)$requete->fetchColumn();

$requete = $bdd->prepare('SELECT COUNT(*) FROM inscriptions');
$requete->execute(array());
$totalInscriptions = (int)$requete->fetchColumn();

$requete = $bdd->prepare('SELECT COUNT(*) FROM associations');
$requete->execute(array());
$totalAssociations = (int)$requete->fetchColumn();

$categories = array(
    array('label' => 'Soir&eacute;e', 'url' => 'Soir%C3%A9e', 'icon' => '&#127881;'),
    array('label' => 'Sport', 'url' => 'Sport', 'icon' => '&#9917;'),
    array('label' => 'Culture', 'url' => 'Culture', 'icon' => '&#127917;'),
    array('label' => 'Conf&eacute;rence', 'url' => 'Conf%C3%A9rence', 'icon' => '&#127908;'),
    array('label' => 'Autre', 'url' => 'Autre', 'icon' => '&#10024;')
);

$titrePage = 'OmnesEvent - Accueil';
include __DIR__ . '/includes/header.php';
?>
<section class="hero landing-hero">
    <canvas id="particles"></canvas>
    <div class="hero-content landing-hero-content">
        <p class="eyebrow">Billetterie et vie de campus</p>
        <h1>Tous les &eacute;v&eacute;nements Omnes, au m&ecirc;me endroit.</h1>
        <p class="hero-subtitle">Soir&eacute;es, sport, culture, conf&eacute;rences — ne rate plus rien.</p>
        <div class="hero-actions">
            <a class="btn" href="<?php echo e(urlSite('/event/list.php')); ?>">Voir les &eacute;v&eacute;nements &rarr;</a>
            <a class="btn ghost" href="<?php echo e(urlSite('/register.php')); ?>">Cr&eacute;er un compte</a>
        </div>
    </div>
</section>

<section class="section featured-section">
    <div class="section-title">
        <div>
            <p class="eyebrow">&Agrave; ne pas manquer</p>
            <h2>Le prochain rendez-vous</h2>
        </div>
    </div>
    <?php if ($prochainEvenement) {
        $affiche = urlSite($prochainEvenement['url_affiche'] ?: '/assets/img/default-event.svg');
        $prixClasse = evenementGratuit($prochainEvenement) ? 'free' : 'paid';
    ?>
        <article class="featured-event panel">
            <a class="featured-poster" href="<?php echo e(urlSite('/event/detail.php?id=' . (int)$prochainEvenement['id'])); ?>">
                <img src="<?php echo e($affiche); ?>" alt="<?php echo e($prochainEvenement['titre']); ?>">
                <span class="badge price-badge <?php echo e($prixClasse); ?>"><?php echo e(libellePrix($prochainEvenement)); ?></span>
            </a>
            <div class="featured-content">
                <span class="badge category-badge"><?php echo e($prochainEvenement['categorie']); ?></span>
                <h2><?php echo e($prochainEvenement['titre']); ?></h2>
                <p><?php echo date('d/m/Y H:i', strtotime($prochainEvenement['date_evenement'])); ?></p>
                <p><?php echo e($prochainEvenement['nom_lieu']); ?></p>
                <p class="muted"><?php echo (int)$prochainEvenement['places_restantes']; ?> place(s) restante(s)</p>
                <a class="btn" href="<?php echo e(urlSite('/event/detail.php?id=' . (int)$prochainEvenement['id'])); ?>">
                    <?php echo estConnecte() ? 'R&eacute;server ma place' : 'Voir l&rsquo;&eacute;v&eacute;nement'; ?>
                </a>
            </div>
        </article>
    <?php } else { ?>
        <div class="panel empty-state">Aucun &eacute;v&eacute;nement pr&eacute;vu pour le moment — revenez bient&ocirc;t !</div>
    <?php } ?>
</section>

<section class="section">
    <div class="section-title">
        <div>
            <p class="eyebrow">Aper&ccedil;u rapide</p>
            <h2>Les 3 prochains &eacute;v&eacute;nements</h2>
        </div>
    </div>
    <div class="preview-grid">
        <?php foreach ($evenementsApercu as $evenement) { include __DIR__ . '/includes/event-card.php'; } ?>
    </div>
    <a class="text-link" href="<?php echo e(urlSite('/event/list.php')); ?>">Voir tous les &eacute;v&eacute;nements &rarr;</a>
</section>

<section class="section">
    <div class="section-title">
        <div>
            <p class="eyebrow">Navigation rapide</p>
            <h2>Cat&eacute;gories</h2>
        </div>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $categorie) { ?>
            <a class="category-tile" href="<?php echo e(urlSite('/event/list.php?categorie=' . $categorie['url'])); ?>">
                <span><?php echo $categorie['icon']; ?></span>
                <strong><?php echo $categorie['label']; ?></strong>
            </a>
        <?php } ?>
    </div>
</section>

<section class="section stats-section">
    <div class="stats-grid">
        <article class="stat-card panel">
            <strong class="stat-number" data-target="<?php echo $totalEvenements; ?>">0</strong>
            <span>&Eacute;v&eacute;nements publi&eacute;s</span>
        </article>
        <article class="stat-card panel">
            <strong class="stat-number" data-target="<?php echo $totalInscriptions; ?>">0</strong>
            <span>Inscriptions</span>
        </article>
        <article class="stat-card panel">
            <strong class="stat-number" data-target="<?php echo $totalAssociations; ?>">0</strong>
            <span>Associations actives</span>
        </article>
    </div>
</section>
<?php
$scripts = array('/assets/js/particles.js');
include __DIR__ . '/includes/footer.php';
?>
