<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Diplome.php';

class DiplomeRepository
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
	public function create(Diplome $diplome)
	{
		$sql = "INSERT INTO diplome 
				(diplome_type_code, diplome_type_libelle, diplome_serie_code, diplome_serie_libelle)
				VALUES 
				(:type_code, :type_libelle, :serie_code, :serie_libelle)
				RETURNING diplome_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':type_code'    , $diplome->getDiplomeTypeCode    ());
		$stmt->bindValue(':type_libelle' , $diplome->getDiplomeTypeLibelle ());
		$stmt->bindValue(':serie_code'   , $diplome->getDiplomeSerieCode   ());
		$stmt->bindValue(':serie_libelle', $diplome->getDiplomeSerieLibelle());

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$diplome->setDiplomeId( $row['diplome_id'] );

		$stmt->execute();
	}

	public function createDiplomeFromRow(array $row): Diplome
	{
		return new Diplome
		(
			$row['diplome_id'           ],
			$row['diplome_type_code'    ],
			$row['diplome_type_libelle' ],
			$row['diplome_serie_code'   ],
			$row['diplome_serie_libelle']
		);
	}

	public function findById($id): ?Diplome
	{
		$sql = "SELECT * FROM diplome WHERE diplome_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createDiplomeFromRow($row); }
		}
		return null;
	}

	public function findAll() : array
	{
		$sql = "SELECT * FROM DIPLOME";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createDiplomeFromRow($row);
		}
		return $result;
	}
}
