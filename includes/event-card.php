<?php
$affiche = urlSite($evenement['url_affiche'] ?: '/assets/img/default-event.svg');
$places = isset($evenement['places_restantes']) ? (int)$evenement['places_restantes'] : placesRestantes($bdd, $evenement);
$prixClasse = evenementGratuit($evenement) ? 'free' : 'paid';
?>
<article class="event-card" data-category="<?php echo e($evenement['categorie']); ?>">
    <a href="<?php echo e(urlSite('/event/detail.php?id=' . (int)$evenement['id'])); ?>" class="poster">
        <img src="<?php echo e($affiche); ?>" alt="<?php echo e($evenement['titre']); ?>">
        <span class="badge category-badge"><?php echo e($evenement['categorie']); ?></span>
        <span class="badge price-badge <?php echo e($prixClasse); ?>"><?php echo e(libellePrix($evenement)); ?></span>
    </a>
    <div class="card-body">
        <h3><?php echo e($evenement['titre']); ?></h3>
        <p><?php echo date('d/m/Y H:i', strtotime($evenement['date_evenement'])); ?> - <?php echo e($evenement['nom_lieu']); ?></p>
        <p class="muted"><?php echo e($places); ?> place(s) disponible(s)</p>
        <a class="btn full" href="<?php echo e(estConnecte() ? urlSite('/event/detail.php?id=' . (int)$evenement['id']) : urlSite('/login.php')); ?>">S'inscrire</a>
    </div>
</article>
