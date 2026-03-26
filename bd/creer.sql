DROP TABLE IF EXISTS FILTRE;
DROP TABLE IF EXISTS DIPLOME_FORMATION;
DROP TABLE IF EXISTS ETABLISSEMENT_FORMATION;
DROP TABLE IF EXISTS FORMATION_SPECIALITE;
DROP TABLE IF EXISTS CANDIDAT;
DROP TABLE IF EXISTS CRITERE;
DROP TABLE IF EXISTS GROUPE;
DROP TABLE IF EXISTS DIPLOME;
DROP TABLE IF EXISTS FORMATION;
DROP TABLE IF EXISTS SPECIALTE;
DROP TABLE IF EXISTS ETABLISSEMENT;
DROP TABLE IF EXISTS LOCALISATION;
DROP TABLE IF EXISTS COMPTE;

CREATE TABLE COMPTE
(
  compte_id      SERIAL       PRIMARY KEY,
  compte_nom     VARCHAR(255) NOT NULL,
  compte_email   VARCHAR(255) NOT NULL,
  compte_mdp     VARCHAR(255) NOT NULL,
  compte_isAdmin BOOLEAN      NOT NULL DEFAULT FALSE
);

CREATE TABLE LOCALISATION
(
  localisation_id          SERIAL       PRIMARY KEY,
  localisation_pays        VARCHAR(255) NULL,
  localisation_code_postal VARCHAR(10)  NULL,
  localisation_commune     VARCHAR(255) NULL,
  localisation_departement VARCHAR(255) NULL
);

CREATE TABLE DIPLOME
(
  diplome_id            INT          PRIMARY KEY,
  diplome_type_code     INT NULL,
  diplome_type_libelle  VARCHAR(255) NULL,
  diplome_serie_code    VARCHAR(255) NULL,
  diplome_serie_libelle VARCHAR(255) NULL
);

CREATE TABLE GROUPE
(
  groupe_id           INT          PRIMARY KEY,
  groupe_nom          VARCHAR(255) NULL,
  groupe_couleur      VARCHAR(  7) NULL,
  groupe_note_dossier FLOAT        NULL
);

CREATE TABLE FORMATION
(
  formation_id  INT          PRIMARY KEY,
  formation_nom VARCHAR(255) NOT NULL
);

CREATE TABLE SPECIALITE
(
  specialite_id  INT          PRIMARY KEY,
  specialite_nom VARCHAR(255) NOT NULL
);

CREATE TABLE CRITERE
(
  critere_id      SERIAL       PRIMARY KEY,
  critere_libelle VARCHAR(255) NOT NULL,
  critere_filtre  VARCHAR(255) NOT NULL,
  critere_min     FLOAT        NULL,
  critere_max     FLOAT        NULL
);

CREATE TABLE ETABLISSEMENT
(
  etablissement_id  INT          PRIMARY KEY,
  etablissement_nom VARCHAR(255) NOT NULL,
  localisation_id   INT NULL,

  FOREIGN KEY (localisation_id) REFERENCES localisation (localisation_id)
);

CREATE TABLE candidat
(
  candidat_code          INT          PRIMARY KEY,
  candidat_nom           VARCHAR(255) NOT NULL,
  candidat_prenom        VARCHAR(255) NOT NULL,
  candidat_civilite      VARCHAR(10)  NULL,
  candidat_profil        VARCHAR(255) DEFAULT 'En terminale',
  candidat_boursier_code INT          DEFAULT 0,
  candidat_note_lycee    REAL         DEFAULT 0,
  candidat_note_fiche    REAL         DEFAULT 0,
  candidat_note_globale  REAL         DEFAULT 0,
  candidat_commentaire   TEXT         NULL,
  diplome_id             INT          NULL,
  etablissement_id       INT          NULL,
  groupe_id              INT          NULL,

  FOREIGN KEY (diplome_id      ) REFERENCES DIPLOME       (diplome_id      ),
  FOREIGN KEY (etablissement_id) REFERENCES ETABLISSEMENT (etablissement_id),
  FOREIGN KEY (groupe_id       ) REFERENCES GROUPE        (groupe_id       )
);


CREATE TABLE FORMATION_SPECIALITE
(
  formation_id      INT         NOT NULL,
  specialite_id     INT         NOT NULL,
  statut_specialite VARCHAR(50) NOT NULL,

  PRIMARY KEY (formation_id, specialite_id, statut_specialite),
  FOREIGN KEY (specialite_id) REFERENCES SPECIALITE (specialite_id),
  FOREIGN KEY (formation_id ) REFERENCES FORMATION  (formation_id )
);

CREATE TABLE ETABLISSEMENT_FORMATION
(
  etablissement_id INT NOT NULL,
  formation_id     INT NOT NULL,

  PRIMARY KEY (etablissement_id, formation_id),
  FOREIGN KEY (etablissement_id) REFERENCES ETABLISSEMENT (etablissement_id),
  FOREIGN KEY (formation_id    ) REFERENCES FORMATION     (formation_id    )
);

CREATE TABLE DIPLOME_FORMATION
(
  diplome_id   INT NOT NULL,
  formation_id INT NOT NULL,

  PRIMARY KEY (diplome_id, formation_id),
  FOREIGN KEY (diplome_id)   REFERENCES DIPLOME   (diplome_id  ),
  FOREIGN KEY (formation_id) REFERENCES FORMATION (formation_id)
);

CREATE TABLE FILTRE
(
  groupe_id  INT NOT NULL,
  critere_id INT NOT NULL,

  PRIMARY KEY (critere_id, groupe_id),
  FOREIGN KEY (critere_id) REFERENCES critere (critere_id),
  FOREIGN KEY (groupe_id )  REFERENCES groupe (groupe_id )
);