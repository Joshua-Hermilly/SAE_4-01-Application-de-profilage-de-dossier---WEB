<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Candidat.php';

class CandidatRepository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*         Constructeur          */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*            Méthodes           */
	/*-------------------------------*/
	public function create(Candidat $candidat)
	{
		$sql = "INSERT INTO candidat 
				(candidat_code, candidat_nom, candidat_prenom, candidat_civilite, candidat_profil, 
				 candidat_boursier_code, candidat_note_lycee, candidat_note_fiche, 
				 candidat_note_global, candidat_commentaire, etablissement_id, groupe_id, diplome_id)
				VALUES 
				(:code, :nom, :prenom, :civilite, :profil, 
				 :boursier, :note_lycee, :note_fiche, 
				 :note_global, :commentaire, :etablissement, :groupe, :diplome)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':code'       , $candidat->getCandidatCode        ());
		$stmt->bindValue(':nom'        , $candidat->getCandidatNom         ());
		$stmt->bindValue(':prenom'     , $candidat->getCandidatPrenom      ());
		$stmt->bindValue(':civilite'   , $candidat->getCandidatCivilite    ());
		$stmt->bindValue(':profil'     , $candidat->getCandidatProfil      ());
		$stmt->bindValue(':boursier'   , $candidat->getCandidatBoursierCode());
		$stmt->bindValue(':note_lycee' , $candidat->getCandidatNoteLycee   ());
		$stmt->bindValue(':note_fiche' , $candidat->getCandidatNoteFiche   ());
		$stmt->bindValue(':note_global', $candidat->getCandidatNoteGlobal  ());
		$stmt->bindValue(':commentaire', $candidat->getCandidatCommentaire ());

		$etablissement_id = $candidat->getEtablissement() ?->getEtablissementId();
		$groupe_id        = $candidat->getGroupe       () ?->getGroupeId       ();
		$diplome_id       = $candidat->getDiplome      () ?->getDiplomeId      ();

		$stmt->bindValue(':etablissement', $etablissement_id);
		$stmt->bindValue(':groupe'       , $groupe_id       );
		$stmt->bindValue(':diplome'      , $diplome_id      );

		$stmt->execute();
	}

	public function update(Candidat $candidat): bool
	{
		$sql = "UPDATE candidat SET
				candidat_nom           = :nom,
				candidat_prenom        = :prenom,
				candidat_civilite      = :civilite,
				candidat_profil        = :profil,
				candidat_boursier_code = :boursier,
				candidat_note_lycee    = :note_lycee,
				candidat_note_fiche    = :note_fiche,
				candidat_note_global   = :note_global,
				candidat_commentaire   = :commentaire,
				etablissement_id       = :etablissement,
				groupe_id              = :groupe,
				diplome_id             = :diplome
				WHERE candidat_code = :code";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':code'       , $candidat->getCandidatCode        ());
		$stmt->bindValue(':nom'        , $candidat->getCandidatNom         ());
		$stmt->bindValue(':prenom'     , $candidat->getCandidatPrenom      ());
		$stmt->bindValue(':civilite'   , $candidat->getCandidatCivilite    ());
		$stmt->bindValue(':profil'     , $candidat->getCandidatProfil      ());
		$stmt->bindValue(':boursier'   , $candidat->getCandidatBoursierCode());
		$stmt->bindValue(':note_lycee' , $candidat->getCandidatNoteLycee   ());
		$stmt->bindValue(':note_fiche' , $candidat->getCandidatNoteFiche   ());
		$stmt->bindValue(':note_global', $candidat->getCandidatNoteGlobal  ());
		$stmt->bindValue(':commentaire', $candidat->getCandidatCommentaire ());

		$etablissement_id = $candidat->getEtablissement() ?->getEtablissementId();
		$groupe_id        = $candidat->getGroupe       () ?->getGroupeId       ();
		$diplome_id       = $candidat->getDiplome      () ?->getDiplomeId      ();

		$stmt->bindValue(':etablissement', $etablissement_id);
		$stmt->bindValue(':groupe'       , $groupe_id       );
		$stmt->bindValue(':diplome'      , $diplome_id      );

		return $stmt->execute();
	}

	public function createCandidatFromRow(array $row): Candidat
	{
		$etablissement = null;
		if (isset($row['etablissement_id']) && $row['etablissement_id'] !== null)
		{
			$etablissement = (new EtablissementRepository())->getEtablissementById($row['etablissement_id']);
		}

		$groupe = null;
		if (isset($row['groupe_id']) && $row['groupe_id'] !== null)
		{
			$groupe = (new GroupeRepository())->getGroupeById($row['groupe_id']);
		}

		$diplome = null;
		if (isset($row['diplome_id']) && $row['diplome_id'] !== null)
		{
			$diplome = (new DiplomeRepository())->getDiplomeById($row['diplome_id']);
		}

		return new Candidat
		(
			(int)$row['candidat_code'],
			$row['candidat_nom'     ],
			$row['candidat_prenom'  ],
			$row['candidat_civilite'],
			$row['candidat_profil'  ],
			(int)$row['candidat_boursier_code'],
			$row['candidat_note_lycee' ] ? (float)$row['candidat_note_lycee' ] : null,
			$row['candidat_note_fiche' ] ? (float)$row['candidat_note_fiche' ] : null,
			$row['candidat_note_global'] ? (float)$row['candidat_note_global'] : null,
			$row['candidat_commentaire'],
			$etablissement,
			$groupe,
			$diplome
		);
	}

	public function findById(int $id): ?Candidat
	{
		$sql  = "SELECT * FROM candidat WHERE candidat_code = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row) { return $this->createCandidatFromRow($row); }
		return null;
	}
}
