<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
exigerConnexion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Methode interdite.');
verifierCSRF();
$id = (int)($_POST['id'] ?? 0);
if ($_SESSION['role'] === 'admin') {
    $requete = $bdd->prepare('DELETE FROM evenements WHERE id = ?');
    $requete->execute(array($id));
    header('Location: /omnesevent/dashboard/admin.php');
} else {
    exigerRole('organisateur');
    $requete = $bdd->prepare('DELETE FROM evenements WHERE id = ? AND cree_par = ?');
    $requete->execute(array($id, $_SESSION['utilisateur_id']));
    header('Location: /omnesevent/dashboard/organizer.php');
}
exit;
?>
