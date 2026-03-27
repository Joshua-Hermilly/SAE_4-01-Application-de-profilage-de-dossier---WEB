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
		$sql = "INSERT INTO specialite (specialite_opt1, specialite_opt2, specialite_spe1, specialite_spe2, specialite_speAbd, id_diplome)
		        VALUES (:opt1, :opt2, :spe1, :spe2, :speAbd, id_diplome)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id'    , $specialite->getSpecialiteId    ());
		$stmt->bindValue(':opt1'  , $specialite->getSpecialiteOpt1  ());
		$stmt->bindValue(':opt2'  , $specialite->getSpecialiteOpt2  ());
		$stmt->bindValue(':spe1'  , $specialite->getSpecialiteSpe1  ());
		$stmt->bindValue(':spe2'  , $specialite->getSpecialiteSpe2  ());
		$stmt->bindValue(':speAbd', $specialite->getSpecialiteSpeAbd());
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row && isset($row['specialite_id']))
		{
			$specialite->setSpecialiteId((int)$row['specialite_id']);
		}
		return $specialite;
	}

	public function createSpecialiteFromRow(array $row): Specialite
	{
		return new Specialite(
			(int)$row['specialite_id'    ],
			     $row['specialite_opt1'  ] ?? null,
			     $row['specialite_opt2'  ] ?? null,
			     $row['specialite_spe1'  ] ?? null,
			     $row['specialite_spe2'  ] ?? null,
			     $row['specialite_speAbd'] ?? null,
			     $row['id_diplome'       ]
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
