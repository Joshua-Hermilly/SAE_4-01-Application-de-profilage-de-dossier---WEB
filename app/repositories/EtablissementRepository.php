<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Localisation.php';
require_once '../app/repositories/LocalisationRepository.php';

class EtablissementRepository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private $pdo;

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
				(etablissement_id, etablissement_nom, localisation_id)
				VALUES 
				(:id, :nom, :localisation)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id' , $etablissement->getEtablissementId());
		$stmt->bindValue(':nom', $etablissement->getEtablissementNom());
		
		$localisation_id = $etablissement->getLocalisation() ? $etablissement->getLocalisation()->getLocalisationId() : null;
		$stmt->bindValue(':localisation', $localisation_id);

		if ($stmt->execute()) { return $etablissement; }
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
			(int)$row['etablissement_id'],
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

	public function getEtablissementsByFormation(int $formation_id): array
	{
		$sql = "SELECT e.*
				FROM etablissement e
				JOIN etablissement_formation ef ON ef.etablissement_id = e.etablissement_id
				WHERE ef.formation_id = :formation_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':formation_id', $formation_id, PDO::PARAM_INT);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$items = [];
		foreach ($rows as $row)
		{
			$items[] = $this->createEtablissementFromRow($row);
		}

		return $items;
	}
}
