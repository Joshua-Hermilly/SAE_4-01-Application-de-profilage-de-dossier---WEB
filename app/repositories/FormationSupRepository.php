<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/FormationSup.php';

class FormationSupSupRepository
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
	public function create(FormationSup $formationSup): ?FormationSup
	{
		$sql = "INSERT INTO formationSup 
				(formation_id, formation_nom)
				VALUES 
				(:id, :nom)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id' , $formationSup->getFormationId());
		$stmt->bindValue(':nom', $formationSup->getFormationNom());

		if ($stmt->execute()) { return $formationSup; }
		return null;
	}

	public function createFormationSupFromRow(array $row): FormationSup
	{
		return new FormationSup(
			$row['formationSup_id'],
			$row['formationSup_nom'],
		);
	}

	public function getFormationSupById($id): ?FormationSup
	{
		$sql = "SELECT * FROM formationSup WHERE formation_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createFormationSupFromRow($row); }
		}
		return null;
	}
}
