<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('organisateur');

$id = (int)($_GET['id'] ?? 0);
$req = $bdd->prepare('SELECT * FROM evenements WHERE id = ? AND cree_par = ?');
$req->execute(array($id, $_SESSION['utilisateur_id']));
$evenement = $req->fetch(PDO::FETCH_ASSOC);
if (!$evenement) die('Evenement introuvable.');

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    $affiche = uploadAffiche('affiche') ?: $evenement['url_affiche'];
    $mode_prix = $_POST['mode_prix'] ?? 'gratuit';
    $prix = $mode_prix === 'payant' ? (float)str_replace(',', '.', $_POST['prix'] ?? 0) : 0;
    if ($prix < 0) {
        $erreur = 'Le prix ne peut pas etre negatif.';
    } else {
        $adresse_lieu = trim($_POST['adresse_lieu'] ?? '');
        if ($adresse_lieu !== ($evenement['adresse_lieu'] ?? '')) {
            list($latitude, $longitude) = geocoderAdresse($adresse_lieu);
        } else {
            $latitude = $evenement['latitude'];
            $longitude = $evenement['longitude'];
        }
        $requete = $bdd->prepare('UPDATE evenements SET titre=?, description=?, categorie=?, date_evenement=?, nom_lieu=?, adresse_lieu=?, latitude=?, longitude=?, url_affiche=?, capacite=?, prix=? WHERE id=? AND cree_par=?');
        $requete->execute(array($_POST['titre'], $_POST['description'], $_POST['categorie'], $_POST['date_evenement'], $_POST['nom_lieu'], $adresse_lieu, $latitude, $longitude, $affiche, (int)$_POST['capacite'], $prix, $id, $_SESSION['utilisateur_id']));
        rediriger('/dashboard/organizer.php');
    }
}
$titrePage = 'Modifier un evenement';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/form.php';
include __DIR__ . '/../includes/footer.php';
?>
