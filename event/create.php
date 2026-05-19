<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerRole('organisateur');

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCSRF();
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categorie = $_POST['categorie'] ?? 'Autre';
    $date_evenement = $_POST['date_evenement'] ?? '';
    $nom_lieu = trim($_POST['nom_lieu'] ?? '');
    $adresse_lieu = trim($_POST['adresse_lieu'] ?? '');
    $capacite = (int)($_POST['capacite'] ?? 0);
    $mode_prix = $_POST['mode_prix'] ?? 'gratuit';
    $prix = $mode_prix === 'payant' ? (float)str_replace(',', '.', $_POST['prix'] ?? 0) : 0;
    $affiche = uploadAffiche('affiche');
    if ($prix < 0) {
        $erreur = 'Le prix ne peut pas etre negatif.';
    } elseif ($titre && $description && $date_evenement && $nom_lieu && $capacite > 0) {
        list($latitude, $longitude) = geocoderAdresse($adresse_lieu);
        $requete = $bdd->prepare('INSERT INTO evenements(titre, description, categorie, date_evenement, nom_lieu, adresse_lieu, latitude, longitude, url_affiche, capacite, prix, cree_par, statut, cree_le) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,"en_attente",NOW())');
        $requete->execute(array($titre, $description, $categorie, $date_evenement, $nom_lieu, $adresse_lieu, $latitude, $longitude, $affiche, $capacite, $prix, $_SESSION['utilisateur_id']));
        rediriger('/dashboard/organizer.php');
    } else {
        $erreur = 'Veuillez completer tous les champs obligatoires.';
    }
}
$titrePage = 'Creer un evenement';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/form.php';
include __DIR__ . '/../includes/footer.php';
?>
