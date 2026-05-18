<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($valeur) {
    return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
}

function baseUrlSite() {
    if (defined('APP_BASE_URL')) {
        return rtrim(APP_BASE_URL, '/');
    }
    $racineDocument = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $racineProjet = realpath(__DIR__ . '/..');
    if ($racineDocument && $racineProjet && strpos(strtolower($racineProjet), strtolower($racineDocument)) === 0) {
        $relatif = trim(str_replace('\\', '/', substr($racineProjet, strlen($racineDocument))), '/');
        return $relatif !== '' ? '/' . $relatif : '';
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $segments = explode('/', trim($script, '/'));
    if (count($segments) === 1 && substr($segments[0], -4) === '.php') {
        return '';
    }
    return !empty($segments[0]) && substr($segments[0], -4) !== '.php' ? '/' . $segments[0] : '';
}

function urlSite($chemin = '') {
    if ($chemin === '') {
        return baseUrlSite() ?: '/';
    }
    if (preg_match('#^(https?:)?//#', $chemin) || strpos($chemin, 'data:') === 0) {
        return $chemin;
    }
    if (strpos($chemin, '/omnesevent/') === 0) {
        $chemin = substr($chemin, strlen('/omnesevent'));
    } elseif ($chemin[0] !== '/') {
        $chemin = '/' . $chemin;
    }
    return baseUrlSite() . $chemin;
}

function rediriger($chemin) {
    header('Location: ' . urlSite($chemin));
    exit;
}

function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

function jetonCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifierCSRF() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        die('Token CSRF invalide.');
    }
}

function getUtilisateur($bdd) {
    if (!estConnecte()) {
        return null;
    }
    $requete = $bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?');
    $requete->execute(array($_SESSION['utilisateur_id']));
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function exigerConnexion() {
    if (!estConnecte()) {
        rediriger('/login.php');
    }
}

function exigerRole($roles) {
    exigerConnexion();
    if (!is_array($roles)) {
        $roles = array($roles);
    }
    if (!in_array($_SESSION['role'], $roles)) {
        rediriger('/index.php');
    }
}

function compterConfirmes($bdd, $evenement_id) {
    $requete = $bdd->prepare("SELECT COUNT(*) FROM inscriptions WHERE evenement_id = ? AND statut = 'confirme'");
    $requete->execute(array($evenement_id));
    return (int)$requete->fetchColumn();
}

function placesRestantes($bdd, $evenement) {
    return max(0, (int)$evenement['capacite'] - compterConfirmes($bdd, $evenement['id']));
}

function promouvoirListeAttente($bdd, $evenement_id) {
    $requete = $bdd->prepare("SELECT id FROM inscriptions WHERE evenement_id = ? AND statut = 'liste_attente' ORDER BY inscrit_le ASC LIMIT 1");
    $requete->execute(array($evenement_id));
    $inscription = $requete->fetch(PDO::FETCH_ASSOC);
    if ($inscription) {
        $maj = $bdd->prepare("UPDATE inscriptions SET statut = 'confirme' WHERE id = ?");
        $maj->execute(array($inscription['id']));
    }
}

function uploadAffiche($champ) {
    if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $typesAutorises = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
    $type = mime_content_type($_FILES[$champ]['tmp_name']);
    if (!isset($typesAutorises[$type])) {
        return null;
    }
    $nom = uniqid('affiche_', true) . '.' . $typesAutorises[$type];
    $destination = __DIR__ . '/../assets/uploads/' . $nom;
    if (move_uploaded_file($_FILES[$champ]['tmp_name'], $destination)) {
        return '/assets/uploads/' . $nom;
    }
    return null;
}
?>
