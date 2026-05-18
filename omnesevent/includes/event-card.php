<?php
$affiche = $evenement['url_affiche'] ?: '/omnesevent/assets/img/default-event.svg';
$places = isset($evenement['places_restantes']) ? (int)$evenement['places_restantes'] : placesRestantes($bdd, $evenement);
?>
<article class="event-card" data-category="<?php echo e($evenement['categorie']); ?>">
    <a href="/omnesevent/event/detail.php?id=<?php echo (int)$evenement['id']; ?>" class="poster">
        <img src="<?php echo e($affiche); ?>" alt="<?php echo e($evenement['titre']); ?>">
        <span class="badge"><?php echo e($evenement['categorie']); ?></span>
    </a>
    <div class="card-body">
        <h3><?php echo e($evenement['titre']); ?></h3>
        <p><?php echo date('d/m/Y H:i', strtotime($evenement['date_evenement'])); ?> - <?php echo e($evenement['nom_lieu']); ?></p>
        <p class="muted"><?php echo e($places); ?> place(s) disponible(s)</p>
        <a class="btn full" href="<?php echo estConnecte() ? '/omnesevent/event/detail.php?id=' . (int)$evenement['id'] : '/omnesevent/login.php'; ?>">S'inscrire</a>
    </div>
</article>
