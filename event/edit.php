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
    $requete = $bdd->prepare('UPDATE evenements SET titre=?, description=?, categorie=?, date_evenement=?, nom_lieu=?, adresse_lieu=?, url_affiche=?, capacite=? WHERE id=? AND cree_par=?');
    $requete->execute(array($_POST['titre'], $_POST['description'], $_POST['categorie'], $_POST['date_evenement'], $_POST['nom_lieu'], $_POST['adresse_lieu'], $affiche, (int)$_POST['capacite'], $id, $_SESSION['utilisateur_id']));
    rediriger('/dashboard/organizer.php');
}
$titrePage = 'Modifier un evenement';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/form.php';
include __DIR__ . '/../includes/footer.php';
?>
