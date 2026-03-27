<?php

require_once __DIR__ . '/../core/Repository.php';
require_once __DIR__ . '/../entities/Etablissement.php';
require_once __DIR__ . '/../entities/Localisation.php';

class EtablissementRepository extends Repository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private PDO $pdo;

	/*-------------------------------*/
	/*         Constructeur          */
	/*-------------------------------*/
	public function __construct() { $this->pdo = Repository::getInstance()->getPDO(); }

	/*-------------------------------*/
	/*            Méthodes           */
	/*-------------------------------*/
	public function create(Etablissement $etablissement): ?Etablissement
	{
		$sql = "INSERT INTO etablissement 
				(etablissement_nom, localisation_id)
				VALUES 
				(:nom, :localisation)
				RETURNING etablissement_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom', $etablissement->getEtablissementNom());
		
		$localisation_id = $etablissement->getLocalisation() ? $etablissement->getLocalisation()->getLocalisationId() : null;
		$stmt->bindValue(':localisation', $localisation_id);

		if ($stmt->execute())
		{
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result)
			{
				$etablissement->setEtablissementId($result['etablissement_id']);
				return $etablissement;
			}
		}
		return null;
	}

	public function createEtablissementFromRow(array $row): Etablissement
	{
		$localisation = null;
		if (isset($row['localisation_id']))
		{
			$localisation = (new LocalisationRepository())->getLocalisationById($row['localisation_id']);
		}

		return new Etablissement(
			$row['etablissement_id'],
			$row['etablissement_nom'],
			[],
			[],
			$localisation
		);
	}

	public function getEtablissementById($id): ?Etablissement
	{
		$sql = "SELECT * FROM etablissement WHERE etablissement_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createEtablissementFromRow($row); }
		}
		return null;
	}
}
