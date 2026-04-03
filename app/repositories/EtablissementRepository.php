<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/repositories/CandidatRepository.php';
require_once '../app/repositories/LocalisationRepository.php';

class EtablissementRepository
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*  Requetes                     */
	/*-------------------------------*/
	private function createEtablissementFromRow(array $row): Etablissement
	{
		$candidats = (new CandidatRepository())->findByEtablissementId((int) $row['etablissement_id']);

		return new Etablissement
		(
			(int) $row['etablissement_id'],
			$row['etablissement_nom'],
			(new LocalisationRepository())->findById( (int) $row['localisation_id']),
			$candidats
		);
	}

	public function create(Etablissement $etablissement): void
	{
		$sql = "INSERT INTO ETABLISSEMENT
				(etablissement_nom, localisation_id)
				VALUES (:nom, :localisation)
				RETURNING etablissement_id";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':nom'         , $etablissement->getEtablissementNom()                     );
		$req->bindValue(':localisation', $etablissement->getLocalisation    ()->getLocalisationId());
		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		$etablissement->setEtablissementId( (int) $row['etablissement_id'] );
	}

	public function creates(array $etablissements): void
	{
		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($etablissements); $cpt++)
		{
			$etablissement = $etablissements[$cpt];
			$valeurBind[]  = "(:nom{$cpt}, :localisation{$cpt})";

			$valeurBrut[":nom{$cpt}"]          = $etablissement->getEtablissementNom();
			$valeurBrut[":localisation{$cpt}"] = $etablissement->getLocalisation()->getLocalisationId();
		}

		$sql = "INSERT INTO ETABLISSEMENT 
				(etablissement_nom, localisation_id)
				VALUES " . implode(', ', $valeurBind) . "
				RETURNING etablissement_id";

		$stmt = $this->pdo->prepare($sql);

		for ($cpt = 0; $cpt < count($etablissements); $cpt++)
		{
			$stmt->bindValue(":nom{$cpt}"         , $valeurBrut[":nom{$cpt}"         ]);
			$stmt->bindValue(":localisation{$cpt}", $valeurBrut[":localisation{$cpt}"]);
		}

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($cpt = 0; $cpt < count($etablissements); $cpt++)
		{
			if (isset($rows[$cpt]))
			{
				$etablissements[$cpt]->setEtablissementId((int) $rows[$cpt]['etablissement_id']);
			}
		}
	}

	public function update(Etablissement $etablissement): void
	{
		$sql = "UPDATE ETABLISSEMENT SET
				etablissement_nom = :nom,
				localisation_id   = :localisation
				WHERE etablissement_id = :id";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':id'          , $etablissement->getEtablissementId ()                     );
		$req->bindValue(':nom'         , $etablissement->getEtablissementNom()                     );
		$req->bindValue(':localisation', $etablissement->getLocalisation    ()->getLocalisationId());
		$req->execute();
	}

	public function findById(int $id): ?Etablissement
	{
		$sql  = "SELECT * FROM ETABLISSEMENT WHERE etablissement_id = :id LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row)
		{
			return $this->createEtablissementFromRow($row);
		}
		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM ETABLISSEMENT";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}

	public function exist(Etablissement $etablissement): int
	{
		$sql = "SELECT * FROM ETABLISSEMENT
         		WHERE etablissement_nom = :nom
         		  AND localisation_id   = :localisation
         		LIMIT 1";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom'         , $etablissement->getEtablissementNom()                     );
		$stmt->bindValue(':localisation', $etablissement->getLocalisation    ()->getLocalisationId());

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $row['etablissement_id']; }
		return -1;
	}

	public function getMaxDistance()
	{
		$sql  = "SELECT MAX(localisation_distance) AS max_distance FROM LOCALISATION";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row ? (float) $row['max_distance'] : -1;
	}

	public function findByDistance($distance)
	{
		$sql = "SELECT 
    				E.*
				FROM   
				    ETABLISSEMENT  AS E
					LEFT JOIN LOCALISATION AS L ON L.localisation_id = E.localisation_id
				WHERE  
				    L.localisation_distance <= :distance";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':distance', $distance);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}
}
