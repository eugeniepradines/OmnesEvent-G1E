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
