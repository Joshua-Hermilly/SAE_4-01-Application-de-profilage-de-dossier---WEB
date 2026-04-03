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
		$sql = "INSERT INTO DIPLOME 
				(diplome_type_code, diplome_type_libelle, diplome_serie_code, diplome_serie_libelle, specialite_id)
				VALUES 
				(:type_code, :type_libelle, :serie_code, :serie_libelle, :specialite_id)
				RETURNING diplome_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':type_code'     , $diplome->getDiplomeTypeCode    ()                   );
		$stmt->bindValue(':type_libelle'  , $diplome->getDiplomeTypeLibelle ()                   );
		$stmt->bindValue(':serie_code'    , $diplome->getDiplomeSerieCode   ()                   );
		$stmt->bindValue(':serie_libelle' , $diplome->getDiplomeSerieLibelle()                   );
		$stmt->bindValue( ':specialite_id', $diplome->getSpecialite         ()->getSpecialiteId());

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$diplome->setDiplomeId((int) $row['diplome_id'] );
	}

	public function creates(array $diplomes)
	{
		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($diplomes); $cpt++) 
		{
			$diplome      = $diplomes[$cpt];
			$valeurBind[] = "(:type_code{$cpt}, :type_libelle{$cpt}, :serie_code{$cpt}, :serie_libelle{$cpt}, :specialite_id{$cpt} )";
			
			$valeurBrut[":type_code{$cpt}"    ] = $diplome->getDiplomeTypeCode    ();
			$valeurBrut[":type_libelle{$cpt}" ] = $diplome->getDiplomeTypeLibelle ();
			$valeurBrut[":serie_code{$cpt}"   ] = $diplome->getDiplomeSerieCode   ();
			$valeurBrut[":serie_libelle{$cpt}"] = $diplome->getDiplomeSerieLibelle();
			$valeurBrut[":specialite_id{$cpt}"] = $diplome->getSpecialite         ()->getSpecialiteId();
		}

		$sql = "INSERT INTO DIPLOME 
				(diplome_type_code, diplome_type_libelle, diplome_serie_code, diplome_serie_libelle, specialite_id)
				VALUES " . implode(', ', $valeurBind) . "
				RETURNING diplome_id";

		$stmt = $this->pdo->prepare($sql);
		
		for ($cpt = 0; $cpt < count($diplomes); $cpt++) 
		{
			$stmt->bindValue(":type_code{$cpt}"    , $valeurBrut[":type_code{$cpt}"    ]);
			$stmt->bindValue(":type_libelle{$cpt}" , $valeurBrut[":type_libelle{$cpt}" ]);
			$stmt->bindValue(":serie_code{$cpt}"   , $valeurBrut[":serie_code{$cpt}"   ]);
			$stmt->bindValue(":serie_libelle{$cpt}", $valeurBrut[":serie_libelle{$cpt}"]);
			$stmt->bindValue(":specialite_id{$cpt}", $valeurBrut[":specialite_id{$cpt}"]);
		}

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($cpt = 0; $cpt < count($diplomes); $cpt++) 
		{
			if (isset($rows[$cpt])) 
			{
				$diplomes[$cpt]->setDiplomeId((int) $rows[$cpt]['diplome_id']);
			}
		}
	}

	public function createDiplomeFromRow(array $row): Diplome
	{
		$candidats = (new CandidatRepository())->findByEtablissementId((int) $row['diplome_id']);

		return new Diplome
		(
			$row['diplome_id'           ],
			$row['diplome_type_code'    ],
			$row['diplome_type_libelle' ],
			$row['diplome_serie_code'   ],
			$row['diplome_serie_libelle'],
			$row['specialite_id'        ],
			$candidats
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
