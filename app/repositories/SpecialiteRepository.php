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
	public function create(Specialite $specialite)
	{
		$sql = "INSERT INTO SPECIALITE 
    				(specialite_opt1, specialite_opt2, specialite_spe1, specialite_spe2, specialite_speabd, diplome_id)
		        VALUES 
		            (:opt1, :opt2, :spe1, :spe2, :speAbd, :diplome_id )
		        RETURNING specialite_id;";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':opt1'      , $specialite->getSpecialiteOpt1  ());
		$stmt->bindValue(':opt2'      , $specialite->getSpecialiteOpt2  ());
		$stmt->bindValue(':spe1'      , $specialite->getSpecialiteSpe1  ());
		$stmt->bindValue(':spe2'      , $specialite->getSpecialiteSpe2  ());
		$stmt->bindValue(':speAbd'    , $specialite->getSpecialiteSpeAbd());
		$stmt->bindValue(':diplome_id', $specialite->getDiplomeId       ());
		$stmt->execute();

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$specialite->setSpecialiteId((int) $row['specialite_id'] );
	}

	public function createSpecialiteFromRow(array $row): Specialite
	{
		return new Specialite(
			(int)$row['specialite_id'    ],
			$row['specialite_opt1'  ],
			$row['specialite_opt2'  ],
			$row['specialite_spe1'  ],
			$row['specialite_spe2'  ],
			$row['specialite_speabd'],
			$row['diplome_id'       ]
		);
	}

	public function findById($id): ?Specialite
	{
		$sql = "SELECT * FROM specialite WHERE specialite_id = :id LIMIT 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createSpecialiteFromRow($row); }
		}
		return null;
	}

	public function findAll()
	{
		$sql = "SELECT * FROM SPECIALITE";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createSpecialiteFromRow($row);
		}
		return $result;
	}

}
