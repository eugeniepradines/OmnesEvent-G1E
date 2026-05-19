# OmnesEvent

Plateforme de billetterie et de gestion d'evenements pour Omnes Education.

## Stack

- HTML5, CSS3 mobile-first
- JavaScript vanilla et jQuery
- PHP 8 pur
- MySQL 8 avec PDO et requetes preparees

## Installation

1. Cloner ou placer le dossier `omnesevent` dans le dossier web Apache.
2. Copier `config.example.php` vers `config.php`.
3. Renseigner les identifiants MySQL dans `config.php`.
4. Importer `schema.sql` dans MySQL.
5. Ouvrir `http://localhost/omnesevent/index.php`.

## Configuration hebergement

Pour mettre le site en ligne, copier `config.hosting.example.php` vers `config.php` sur le serveur puis remplacer uniquement le mot de passe MySQL.

Parametres de la base fournis par l'hebergeur :

- Hote : `fdb1031.your-hosting.net`
- Port : `3306`
- Base de donnees : `4760347_omnes`
- Utilisateur : `4760347_omnes`
- Mot de passe : celui defini dans le panneau d'hebergement

## Comptes de test

Mot de passe commun : `password`

- Admin : `admin@omnes.fr`
- Organisateur : `bde@omnes.fr`
- Organisateur : `sport@omnes.fr`
- Participant : `alice@omnes.fr`
- Participant : `hugo@omnes.fr`
- Participant : `ines@omnes.fr`

## Fonctionnalites MVP

- Accueil avec hero canvas, recherche et filtres AJAX.
- Inscription, connexion, deconnexion, sessions et roles.
- Catalogue et detail evenement.
- Reservation AJAX, annulation, liste d'attente et QR code.
- Creation, modification et suppression d'evenements organisateur.
- Profil et billets participant.
- Dashboards organisateur et admin.
- CSRF sur les formulaires POST et requetes PDO preparees.
