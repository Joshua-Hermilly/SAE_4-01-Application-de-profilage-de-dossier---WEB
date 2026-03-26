<?php

use entities\Candidat;

require_once __DIR__ . '/../core/Repository.php';
require_once __DIR__ . '/../entities/Candidat.php';

class CandidatRepository extends Repository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private PDO $pdo;

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
	public function create(Candidat $candidat): ?Candidat
	{
		try {
			$sql = "INSERT INTO candidat 
					(candidat_nom, candidat_prenom, candidat_civilite, candidat_profil, 
					 candidat_boursier_code, candidat_note_lycee, candidat_note_fiche, 
					 candidat_note_global, candidat_commentaire, etablissement_code, groupe_code, diplome_code)
					VALUES 
					(:nom, :prenom, :civilite, :profil, 
					 :boursier, :note_lycee, :note_fiche, 
					 :note_global, :commentaire, :etablissement, :groupe, :diplome)
					RETURNING candidat_code";

			$stmt = $this->pdo->prepare($sql);

			$stmt->bindValue(':nom'        , $candidat->getCandidatNom()         , PDO::PARAM_STR);
			$stmt->bindValue(':prenom'     , $candidat->getCandidatPrenom()      , PDO::PARAM_STR);
			$stmt->bindValue(':civilite'   , $candidat->getCandidatCivilite()    , PDO::PARAM_STR);
			$stmt->bindValue(':profil'     , $candidat->getCandidatProfil()      , PDO::PARAM_STR);
			$stmt->bindValue(':boursier'   , $candidat->getCandidatBoursierCode(), PDO::PARAM_INT);
			$stmt->bindValue(':note_lycee' , $candidat->getCandidatNoteLycee()   , PDO::PARAM_NULL);
			$stmt->bindValue(':note_fiche' , $candidat->getCandidatNoteFiche()   , PDO::PARAM_NULL);
			$stmt->bindValue(':note_global', $candidat->getCandidatNoteGlobal()  , PDO::PARAM_NULL);
			$stmt->bindValue(':commentaire', $candidat->getCandidatCommentaire() , PDO::PARAM_NULL);
			
			$etablissement_code = $candidat->getEtablissement() ? $candidat->getEtablissement()->getEtablissementId() : null;
			$groupe_code        = $candidat->getGroupe() ? $candidat->getGroupe()->getGroupeId() : null;
			$diplome_code       = $candidat->getDiplome()->getDiplomeId();

			$stmt->bindValue(':etablissement', $etablissement_code, PDO::PARAM_INT);
			$stmt->bindValue(':groupe'       , $groupe_code       , PDO::PARAM_INT);
			$stmt->bindValue(':diplome'      , $diplome_code      , PDO::PARAM_INT);

			if ($stmt->execute())
			{
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($result)
				{
					$candidat->setCandidatCode($result['candidat_code']);
					return $candidat;
				}
			}

			return null;
		}
		catch (PDOException $e)
		{
			error_log("Erreur CandidatRepository::create : " . $e->getMessage());
			throw $e;
		}

	}

	public function createCandidatFromRow( array $row): Candidat
	{
		try
		{
			$etablissement = null;
			if (isset($row['etablissement_code']))
			{
				$etablissement = (new EtablissementRepository())->getEtablissementById($row['etablissement_code']);
			}

			$groupe = null;
			if (isset($row['groupe_code']))	
			{
				$groupe = (new GroupeRepository())->getGroupeById($row['groupe_code']);
			}

			$diplome = null;
			if (isset($row['diplome_code']))
			{
				$diplome = (new DiplomeRepository())->getDiplomeById($row['diplome_code']);
			}
		}
		catch (PDOException $e)
		{
			error_log("Erreur CandidatRepository::createCandidatFromRow : " . $e->getMessage());
			throw $e;
		}


		return new Candidat(
			$row['candidat_code'],
			$row['candidat_nom'],
			$row['candidat_prenom'],
			$row['candidat_civilite'],
			$row['candidat_profil'],
			$row['candidat_boursier_code'],
			$row['candidat_note_lycee'],
			$row['candidat_note_fiche'],
			$row['candidat_note_global'],
			$row['candidat_commentaire'],
			$etablissement,
			$diplome,
			$groupe
		);
	}

	public function findbyId($id): ?Candidat
	{
		try
		{
			$sql = "SELECT * FROM candidat WHERE candidat_code = :id";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			if ($stmt->execute())
			{
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($row)
				{
					return $this->createCandidatFromRow($row);
				}
			}

			return null;
		}
		catch (PDOException $e)
		{
			error_log("Erreur CandidatRepository::findById : " . $e->getMessage());
			throw $e;
		}
	}
}
