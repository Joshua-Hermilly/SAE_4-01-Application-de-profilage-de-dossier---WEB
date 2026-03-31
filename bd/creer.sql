-- Suppression des tables dans l'ordre des dépendances
DROP TABLE IF EXISTS FILTRE;
DROP TABLE IF EXISTS CANDIDAT;
DROP TABLE IF EXISTS SPECIALITE;
DROP TABLE IF EXISTS FORMATION_SUP;
DROP TABLE IF EXISTS ETABLISSEMENT;
DROP TABLE IF EXISTS LOCALISATION;
DROP TABLE IF EXISTS GROUPE;
DROP TABLE IF EXISTS CRITERE;
DROP TABLE IF EXISTS DIPLOME;
DROP TABLE IF EXISTS COMPTE;

-- Table COMPTE
CREATE TABLE COMPTE (
  compte_identifiant VARCHAR(255) PRIMARY KEY,
  compte_mdp         VARCHAR(255) NOT NULL,
  compte_isAdmin     BOOLEAN      NOT NULL DEFAULT FALSE
);
INSERT INTO COMPTE (compte_identifiant, compte_mdp, compte_isAdmin)
VALUES             ('admin', '$2y$12$rNJ8W0ubDoYEQHhhhdp7QOAuZlYLPqhu.903Qclctpg2damKaEVn6', TRUE);


-- Table LOCALISATION
CREATE TABLE LOCALISATION (
  localisation_id          SERIAL PRIMARY KEY,
  localisation_pays        VARCHAR(255),
  localisation_code_postal VARCHAR(10),
  localisation_commune     VARCHAR(255),
  localisation_departement VARCHAR(255)
);

-- Table ETABLISSEMENT
CREATE TABLE ETABLISSEMENT (
  etablissement_id  SERIAL PRIMARY KEY,
  etablissement_nom VARCHAR(255) NOT NULL,
  localisation_id   INT,
  FOREIGN KEY (localisation_id) REFERENCES LOCALISATION(localisation_id)
);

-- Table GROUPE
CREATE TABLE GROUPE (
  groupe_id           INT PRIMARY KEY,
  groupe_nom          VARCHAR(255),
  groupe_couleur      VARCHAR(7),
  groupe_note_dossier FLOAT
);

-- Table CRITERE
CREATE TABLE CRITERE (
  critere_id      SERIAL PRIMARY KEY,
  critere_libelle VARCHAR(255) NOT NULL,
  critere_filtre  VARCHAR(255) NOT NULL,
  critere_min     FLOAT,
  critere_max     FLOAT
);

-- Table DIPLOME
CREATE TABLE DIPLOME (
  diplome_id            SERIAL PRIMARY KEY,
  diplome_type_code     INT,
  diplome_type_libelle  VARCHAR(255),
  diplome_serie_code    VARCHAR(255),
  diplome_serie_libelle VARCHAR(255)
);

-- Table SPECIALITE
CREATE TABLE SPECIALITE (
  specialite_id     SERIAL PRIMARY KEY,
  specialite_opt1   VARCHAR(255),
  specialite_opt2   VARCHAR(255),
  specialite_spe1   VARCHAR(255),
  specialite_spe2   VARCHAR(255),
  specialite_speAbd VARCHAR(255),
  diplome_id        INT NOT NULL,
  FOREIGN KEY (diplome_id) REFERENCES DIPLOME(diplome_id)
);

-- Table FORMATION_SUP
CREATE TABLE FORMATION_SUP (
  formation_id  SERIAL PRIMARY KEY,
  formation_nom VARCHAR(255) NOT NULL
);

-- Table CANDIDAT
CREATE TABLE CANDIDAT (
  candidat_code          INT PRIMARY KEY,
  candidat_nom           VARCHAR(255) NOT NULL,
  candidat_prenom        VARCHAR(255) NOT NULL,
  candidat_civilite      VARCHAR(10) CHECK (candidat_civilite IN ('M.', 'Mme')),
  candidat_profil        VARCHAR(255) DEFAULT 'En terminale',
  candidat_boursier_code INT CHECK (candidat_boursier_code IN (0, 1, 2)) DEFAULT 0,
  candidat_note_lycee    FLOAT DEFAULT 0,
  candidat_note_fiche    FLOAT DEFAULT 0,
  candidat_note_globale  FLOAT DEFAULT 0,
  candidat_commentaire   TEXT,
  candidat_annee         INT,
  diplome_id             INT,
  etablissement_id       INT,
  formation_id           INT,
  groupe_id              INT,
  FOREIGN KEY (diplome_id) REFERENCES DIPLOME(diplome_id),
  FOREIGN KEY (etablissement_id) REFERENCES ETABLISSEMENT(etablissement_id),
  FOREIGN KEY (formation_id) REFERENCES FORMATION_SUP(formation_id),
  FOREIGN KEY (groupe_id) REFERENCES GROUPE(groupe_id)
);

-- Table FILTRE
CREATE TABLE FILTRE (
  groupe_id  INT NOT NULL,
  critere_id INT NOT NULL,
  PRIMARY KEY (critere_id, groupe_id),
  FOREIGN KEY (critere_id) REFERENCES CRITERE(critere_id),
  FOREIGN KEY (groupe_id) REFERENCES GROUPE(groupe_id)
);