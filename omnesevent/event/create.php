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
    $affiche = uploadAffiche('affiche');
    if ($titre && $description && $date_evenement && $nom_lieu && $capacite > 0) {
        $requete = $bdd->prepare('INSERT INTO evenements(titre, description, categorie, date_evenement, nom_lieu, adresse_lieu, url_affiche, capacite, cree_par, statut, cree_le) VALUES(?,?,?,?,?,?,?,?,?,"en_attente",NOW())');
        $requete->execute(array($titre, $description, $categorie, $date_evenement, $nom_lieu, $adresse_lieu, $affiche, $capacite, $_SESSION['utilisateur_id']));
        header('Location: /omnesevent/dashboard/organizer.php');
        exit;
    }
    $erreur = 'Veuillez completer tous les champs obligatoires.';
}
$titrePage = 'Creer un evenement';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/form.php';
include __DIR__ . '/../includes/footer.php';
?>
