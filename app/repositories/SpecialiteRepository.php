<?php

require_once __DIR__ . '/../core/Repository.php';
require_once __DIR__ . '/../entities/Specialite.php';

class SpecialiteRepository extends Repository
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
	public function create(Specialite $specialite): ?Specialite
	{
		$sql = "INSERT INTO specialite 
				(specialite_nom)
				VALUES 
				(:nom)
				RETURNING specialite_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom', $specialite->getSpecialiteNom());

		if ($stmt->execute())
		{
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result)
			{
				$specialite->setSpecialiteId($result['specialite_id']);
				return $specialite;
			}
		}
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
}
