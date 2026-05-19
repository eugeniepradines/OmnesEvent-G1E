<section class="form-page">
    <form class="panel event-form validate-form" method="post" enctype="multipart/form-data">
        <h1><?php echo e($titrePage); ?></h1>
        <?php if (!empty($erreur)) { ?><p class="alert"><?php echo e($erreur); ?></p><?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo e(jetonCSRF()); ?>">
        <fieldset>
            <legend>Infos</legend>
            <label>Titre<input type="text" name="titre" required value="<?php echo e($evenement['titre'] ?? ''); ?>"></label>
            <label>Description<textarea name="description" required><?php echo e($evenement['description'] ?? ''); ?></textarea></label>
            <label>Categorie
                <select name="categorie">
                    <?php foreach (array('Soirée','Sport','Culture','Conférence','Autre') as $cat) { ?>
                        <option value="<?php echo e($cat); ?>" <?php if (($evenement['categorie'] ?? '') === $cat) echo 'selected'; ?>><?php echo e($cat); ?></option>
                    <?php } ?>
                </select>
            </label>
        </fieldset>
        <fieldset>
            <legend>Date & Lieu</legend>
            <label>Date<input type="datetime-local" name="date_evenement" required value="<?php echo isset($evenement['date_evenement']) ? date('Y-m-d\TH:i', strtotime($evenement['date_evenement'])) : ''; ?>"></label>
            <label>Lieu<input type="text" name="nom_lieu" required value="<?php echo e($evenement['nom_lieu'] ?? ''); ?>"></label>
            <label>Adresse<input type="text" name="adresse_lieu" value="<?php echo e($evenement['adresse_lieu'] ?? ''); ?>"></label>
        </fieldset>
        <fieldset>
            <legend>Affiche, capacite & prix</legend>
            <label>Affiche<input type="file" name="affiche" accept="image/jpeg,image/png,image/webp" class="preview-input"></label>
            <img class="preview-img" src="<?php echo e(!empty($evenement['url_affiche']) ? urlSite($evenement['url_affiche']) : ''); ?>" alt="">
            <label>Capacite<input type="number" name="capacite" min="1" required value="<?php echo e($evenement['capacite'] ?? ''); ?>"></label>
            <?php $prixActuel = prixEvenement($evenement ?? array()); ?>
            <div class="price-toggle" data-price-toggle>
                <label><input type="radio" name="mode_prix" value="gratuit" <?php if ($prixActuel <= 0) echo 'checked'; ?>> Gratuit</label>
                <label><input type="radio" name="mode_prix" value="payant" <?php if ($prixActuel > 0) echo 'checked'; ?>> Payant</label>
            </div>
            <label class="price-field" data-price-field>Prix en euros<input type="number" name="prix" min="0" step="0.01" value="<?php echo e(number_format($prixActuel, 2, '.', '')); ?>"></label>
        </fieldset>
        <button class="btn full" type="submit">Enregistrer</button>
    </form>
</section>
