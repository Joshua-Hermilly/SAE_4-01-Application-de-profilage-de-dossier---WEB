<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Localisation.php';

class LocalisationRepository
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
	private function createLocalisationFromRow(array $row): Localisation
	{
		return new Localisation
		(
			(int) $row['localisation_id'],
			$row['localisation_pays'       ],
			$row['localisation_code_postal'],
			$row['localisation_commune'    ],
			$row['localisation_departement']
		);
	}

	public function create(Localisation $localisation): void
	{
		$sql = "INSERT INTO LOCALISATION
				(localisation_pays, localisation_code_postal, localisation_commune, localisation_departement)
				VALUES (:pays, :code_postal, :commune, :departement)";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':pays'       , $localisation->getLocalisationPays       ());
		$req->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
		$req->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
		$req->bindValue(':departement', $localisation->getLocalisationDepartement());
		$req->execute();

		$localisation->setLocalisationId((int) $this->pdo->lastInsertId());
	}

	public function findById(int $id): ?Localisation
	{
		$sql  = "SELECT * FROM localisation WHERE localisation_id = :id LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createLocalisationFromRow($row); }

		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM LOCALISATION";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createLocalisationFromRow($row);
		}
		return $result;
	}

	public function exist(Localisation $localisation): int
	{
		$sql  = "SELECT * FROM localisation 
                 WHERE localisation_pays        = :pays
				   AND localisation_code_postal = :code_postal
				   AND localisation_commune     = :commune
				   AND localisation_departement = :departement
				 LIMIT 1";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':pays'       , $localisation->getLocalisationPays       ());
		$stmt->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
		$stmt->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
		$stmt->bindValue(':departement', $localisation->getLocalisationDepartement());

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $row['localisation_id']; }
		return -1;
	}
	}
