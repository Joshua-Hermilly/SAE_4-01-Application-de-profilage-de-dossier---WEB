<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Formation.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Diplome.php';

class FormationRepository
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
	public function create(Formation $formation): ?Formation
	{
		$sql = "INSERT INTO formation 
				(formation_nom)
				VALUES 
				(:nom)
				RETURNING formation_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom', $formation->getFormationNom());

		if ($stmt->execute())
		{
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result)
			{
				$formation->setFormationId($result['formation_id']);
				return $formation;
			}
		}
		return null;
	}

	public function createFormationFromRow(array $row): Formation
	{
		$etablissements = [];
		$specialites = [];
		$diplomes = [];

		$etablissements = (new EtablissementRepository())->getEtablissementsByFormation($row['formation_id']);
		$specialites    = (new SpecialiteRepository   ())->getSpecialitesByFormation   ($row['formation_id']);
		$diplomes       = (new DiplomeRepository      ())->getDiplomesByFormation      ($row['formation_id']);

		return new Formation(
			$row['formation_id'],
			$row['formation_nom'],
			$etablissements,
			$specialites,
			$diplomes
		);
	}

	public function getFormationById($id): ?Formation
	{
		$sql = "SELECT * FROM formation WHERE formation_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createFormationFromRow($row); }
		}
		return null;
	}
}
