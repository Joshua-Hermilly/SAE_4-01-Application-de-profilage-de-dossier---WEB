<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Groupe.php';
require_once '../app/entities/Critere.php';
require_once '../app/repositories/CritereRepository.php';

class GroupeRepository
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
	public function create(Groupe $groupe): ?Groupe
	{
		$sql = "INSERT INTO groupe (groupe_id, groupe_nom) VALUES (:id, :nom)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id' , $groupe->getGroupeId());
		$stmt->bindValue(':nom', $groupe->getGroupeNom());

		if ($stmt->execute()) { return $groupe; }
		return null;
	}

	private function createGroupeFromRow(array $row): Groupe
	{
		$criteres = (new CritereRepository())->findByGroupe((int)$row['groupe_id']);

		return new Groupe
		(
			(int)$row['groupe_id'],
			$row['groupe_nom'],
			$criteres,
			[]
		);
	}

	public function getGroupeById(int $id): ?Groupe
	{
		$sql  = "SELECT * FROM groupe WHERE groupe_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createGroupeFromRow($row); }

		return null;
	}
}