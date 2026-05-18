DROP DATABASE IF EXISTS omnesevent;
CREATE DATABASE omnesevent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE omnesevent;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    role ENUM('admin','organisateur','participant') DEFAULT 'participant',
    est_approuve TINYINT(1) DEFAULT 0,
    avatar_url VARCHAR(255),
    cree_le DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    categorie ENUM('Soirée','Sport','Culture','Conférence','Autre') NOT NULL,
    date_evenement DATETIME NOT NULL,
    nom_lieu VARCHAR(180) NOT NULL,
    adresse_lieu VARCHAR(255),
    url_affiche VARCHAR(255),
    capacite INT NOT NULL,
    cree_par INT NOT NULL,
    statut ENUM('en_attente','publie','annule') DEFAULT 'publie',
    cree_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_evenements_utilisateurs FOREIGN KEY (cree_par) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    statut ENUM('confirme','liste_attente','annule') DEFAULT 'confirme',
    token_qr VARCHAR(64) NOT NULL UNIQUE,
    inscrit_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    presente TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_inscriptions_evenements FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    CONSTRAINT fk_inscriptions_utilisateurs FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE associations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(180) NOT NULL,
    url_logo VARCHAR(255),
    organisateur_id INT NOT NULL,
    CONSTRAINT fk_associations_utilisateurs FOREIGN KEY (organisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

INSERT INTO utilisateurs(email, mot_de_passe_hash, prenom, nom, role, est_approuve, avatar_url, cree_le) VALUES
('admin@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Admin', 'Omnes', 'admin', 1, NULL, NOW()),
('bde@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Lea', 'Martin', 'organisateur', 1, NULL, NOW()),
('sport@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Nassim', 'Diallo', 'organisateur', 1, NULL, NOW()),
('alice@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Alice', 'Moreau', 'participant', 1, NULL, NOW()),
('hugo@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Hugo', 'Bernard', 'participant', 1, NULL, NOW()),
('ines@omnes.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'Ines', 'Petit', 'participant', 1, NULL, NOW());

INSERT INTO associations(nom, url_logo, organisateur_id) VALUES
('BDE Omnes', NULL, 2),
('Omnes Sport', NULL, 3);

INSERT INTO evenements(titre, description, categorie, date_evenement, nom_lieu, adresse_lieu, url_affiche, capacite, cree_par, statut, cree_le) VALUES
('Soiree Neon Campus', 'Une soiree etudiante lumineuse avec DJ set et animations.', 'Soirée', '2026-06-12 20:30:00', 'Atrium Omnes', '10 rue Sextius Michel, Paris', '/omnesevent/assets/img/default-event.svg', 120, 2, 'publie', NOW()),
('Tournoi Futsal Interpromo', 'Equipes mixtes, phase de poules puis finale.', 'Sport', '2026-06-18 18:00:00', 'Gymnase Omnes', 'Campus Lyon', '/omnesevent/assets/img/default-event.svg', 60, 3, 'publie', NOW()),
('Expo Photo Campus', 'Exposition des meilleurs cliches realises par les etudiants.', 'Culture', '2026-06-22 12:00:00', 'Hall principal', 'Campus Paris', '/omnesevent/assets/img/default-event.svg', 80, 2, 'publie', NOW()),
('Conference IA & Ethique', 'Rencontre autour des usages responsables de l intelligence artificielle.', 'Conférence', '2026-07-02 17:30:00', 'Amphi A', 'Campus Paris', '/omnesevent/assets/img/default-event.svg', 100, 2, 'publie', NOW()),
('Afterwork Alumni', 'Temps reseau entre etudiants et anciens diplomes.', 'Autre', '2026-07-09 19:00:00', 'Rooftop Omnes', 'Campus Lyon', '/omnesevent/assets/img/default-event.svg', 90, 2, 'publie', NOW()),
('Run Solidaire', 'Course de 5 km au profit d une association partenaire.', 'Sport', '2026-07-16 09:30:00', 'Parc Blandan', 'Lyon', '/omnesevent/assets/img/default-event.svg', 150, 3, 'publie', NOW());

INSERT INTO inscriptions(evenement_id, utilisateur_id, statut, token_qr, inscrit_le, presente) VALUES
(1, 4, 'confirme', 'qr_alice_neon_2026', NOW(), 0),
(1, 5, 'confirme', 'qr_hugo_neon_2026', NOW(), 0),
(2, 4, 'confirme', 'qr_alice_futsal_2026', NOW(), 0),
(3, 6, 'confirme', 'qr_ines_expo_2026', NOW(), 0),
(4, 5, 'confirme', 'qr_hugo_ia_2026', NOW(), 0);
