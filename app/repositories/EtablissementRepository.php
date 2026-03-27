<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Etablissement.php';

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
			$row['localisation_id'] !== null ? (int) $row['localisation_id'] : null,
			$candidats
		);
	}

	public function create(Etablissement $etablissement): void
	{
		$sql = "INSERT INTO ETABLISSEMENT
				(etablissement_nom, localisation_id)
				VALUES (:nom, :localisation)";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':nom'         , $etablissement->getEtablissementNom()                     );
		$req->bindValue(':localisation', $etablissement->getLocalisation    ()->getLocalisationId());
		$req->execute();
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
		$sql  = "SELECT * FROM ETABLISSEMENT WHERE etablissement_id = :id";
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
		$sql = "SELECT * FROM CANDIDAT";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}
}
