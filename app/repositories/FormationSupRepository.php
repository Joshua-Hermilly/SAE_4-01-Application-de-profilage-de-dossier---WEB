<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/FormationSup.php';

class FormationSupRepository
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
	public function create(FormationSup $formationSup)
	{
		$sql = "INSERT INTO FORMATION_SUP 
				(formation_nom, formation_lib)
				VALUES 
				(:nom, :lib)
				RETURNING formation_id;";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom', $formationSup->getFormationNom());
		$stmt->bindValue(':lib', $formationSup->getFormationLib());

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$formationSup->setFormationId((int) $row['formation_id'] );
	}

	public function creates(array $formationSups)
	{
		if (empty($formationSups)) { return; }

		$taillePartie = 500;
		$total        = count($formationSups);

		for ($offset = 0; $offset < $total; $offset += $taillePartie)
		{
			$partie = array_slice($formationSups, $offset, $taillePartie);
			$this->insertChunk($partie);
		}
	}

	private function insertChunk(array $formationSups): void
	{
		if (empty($formationSups)) { return; }

		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($formationSups); $cpt++)
		{
			$formationSup = $formationSups[$cpt];
			$valeurBind[] = "(:nom{$cpt}, :lib{$cpt})";

			$valeurBrut[":nom{$cpt}"] = $formationSup->getFormationNom();
			$valeurBrut[":lib{$cpt}"] = $formationSup->getFormationLib();
		}

		$sql = "INSERT INTO FORMATION_SUP
				(formation_nom, formation_lib)
				VALUES " . implode(', ', $valeurBind) . "
				ON CONFLICT ON CONSTRAINT formation_sup_unique DO UPDATE SET
					formation_nom = EXCLUDED.formation_nom,
					formation_lib = EXCLUDED.formation_lib
				RETURNING formation_id";

		$stmt = $this->pdo->prepare($sql);

		for ($cpt = 0; $cpt < count($formationSups); $cpt++)
		{
			$stmt->bindValue(":nom{$cpt}", $valeurBrut[":nom{$cpt}"]);
			$stmt->bindValue(":lib{$cpt}", $valeurBrut[":lib{$cpt}"]);
		}

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($cpt = 0; $cpt < count($formationSups); $cpt++)
		{
			if (isset($rows[$cpt]))
			{
				$formationSups[$cpt]->setFormationId((int) $rows[$cpt]['formation_id']);
			}
		}
	}

	public function createFormationSupFromRow(array $row): FormationSup
	{
		return new FormationSup
		(
			$row['formation_id' ],
			$row['formation_nom'],
			$row['formation_lib'],
		);
	}

	public function findById($id): ?FormationSup
	{
		$sql = "SELECT * FROM FORMATION_SUP WHERE formation_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createFormationSupFromRow($row); }
		}
		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM FORMATION_SUP";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createFormationSupFromRow($row);
		}
		return $result;
	}
}
