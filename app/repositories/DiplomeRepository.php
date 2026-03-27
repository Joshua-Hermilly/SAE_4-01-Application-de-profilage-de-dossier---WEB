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
	public function __construct() { $this->pdo = Repository::getInstance()->getPDO(); }

	/*-------------------------------*/
	/*            Méthodes           */
	/*-------------------------------*/
	public function create(Diplome $diplome): ?Diplome
	{
		$sql = "INSERT INTO diplome 
				(diplome_type_code, diplome_type_libelle, diplome_serie_code, diplome_serie_libelle)
				VALUES 
				(:type_code, :type_libelle, :serie_code, :serie_libelle)
				RETURNING diplome_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':type_code'    , $diplome->getDiplomeTypeCode()    );
		$stmt->bindValue(':type_libelle' , $diplome->getDiplomeTypeLibelle() );
		$stmt->bindValue(':serie_code'   , $diplome->getDiplomeSerieCode()   );
		$stmt->bindValue(':serie_libelle', $diplome->getDiplomeSerieLibelle());

		if ($stmt->execute())
		{
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result)
			{
				$diplome->setDiplomeId($result['diplome_id']);
				return $diplome;
			}
		}
		return null;
	}

	public function createDiplomeFromRow(array $row): Diplome
	{
		return new Diplome(
			$row['diplome_id'           ],
			$row['diplome_type_code'    ],
			$row['diplome_type_libelle' ],
			$row['diplome_serie_code'   ],
			$row['diplome_serie_libelle']
		);
	}

	public function getDiplomeById($id): ?Diplome
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
}
