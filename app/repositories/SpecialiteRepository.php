<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Specialite.php';

class SpecialiteRepository
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
	public function create(Specialite $specialite): ?Specialite
	{
		$sql = "INSERT INTO specialite 
				(specialite_id, specialite_nom)
				VALUES 
				(:id, :nom)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id' , $specialite->getSpecialiteId());
		$stmt->bindValue(':nom', $specialite->getSpecialiteNom());

		if ($stmt->execute()) { return $specialite; }
		return null;
	}

	public function createSpecialiteFromRow(array $row): Specialite
	{
		return new Specialite(
			$row['specialite_id'],
			$row['specialite_nom']
		);
	}

	public function getSpecialiteById($id): ?Specialite
	{
		$sql = "SELECT * FROM specialite WHERE specialite_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createSpecialiteFromRow($row); }
		}
		return null;
	}

	public function getSpecialitesByFormation(int $formation_id): array
	{
		$sql = "SELECT s.*
				FROM specialite s
				JOIN formation_specialite fs ON fs.specialite_id = s.specialite_id
				WHERE fs.formation_id = :formation_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':formation_id', $formation_id, PDO::PARAM_INT);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$items = [];
		foreach ($rows as $row)
		{
			$items[] = $this->createSpecialiteFromRow($row);
		}

		return $items;
	}
}
