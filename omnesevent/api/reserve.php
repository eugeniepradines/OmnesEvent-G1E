<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

if (!estConnecte()) {
    echo json_encode(array('ok' => false, 'message' => 'Connexion obligatoire.'));
    exit;
}
verifierCSRF();
$evenement_id = (int)($_POST['evenement_id'] ?? 0);

$requete = $bdd->prepare('SELECT * FROM evenements WHERE id = ? AND statut = "publie"');
$requete->execute(array($evenement_id));
$evenement = $requete->fetch(PDO::FETCH_ASSOC);
if (!$evenement) {
    echo json_encode(array('ok' => false, 'message' => 'Evenement introuvable.'));
    exit;
}

$req = $bdd->prepare('SELECT * FROM inscriptions WHERE evenement_id = ? AND utilisateur_id = ? AND statut <> "annule"');
$req->execute(array($evenement_id, $_SESSION['utilisateur_id']));
$inscription = $req->fetch(PDO::FETCH_ASSOC);

if ($inscription) {
    $maj = $bdd->prepare("UPDATE inscriptions SET statut = 'annule' WHERE id = ?");
    $maj->execute(array($inscription['id']));
    promouvoirListeAttente($bdd, $evenement_id);
    echo json_encode(array('ok' => true, 'message' => 'Reservation annulee.', 'action' => 'annule', 'places' => placesRestantes($bdd, $evenement)));
    exit;
}

$statut = placesRestantes($bdd, $evenement) > 0 ? 'confirme' : 'liste_attente';
$token = bin2hex(random_bytes(32));
$ajout = $bdd->prepare('INSERT INTO inscriptions(evenement_id, utilisateur_id, statut, token_qr, inscrit_le, presente) VALUES(?,?,?,?,NOW(),0)');
$ajout->execute(array($evenement_id, $_SESSION['utilisateur_id'], $statut, $token));
echo json_encode(array('ok' => true, 'message' => $statut === 'confirme' ? 'Reservation confirmee.' : 'Vous etes en liste d attente.', 'action' => $statut, 'places' => placesRestantes($bdd, $evenement)));
?>
