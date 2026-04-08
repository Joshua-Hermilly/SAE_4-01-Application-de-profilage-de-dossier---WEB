<?php

require_once '../app/core/Repository.php';
require_once '../app/repositories/SpecialiteRepository.php';
require_once '../app/entities/Diplome.php';
require_once '../app/entities/Specialite.php';


class DiplomeRepository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private $pdo;
	private $specialiteRepository;

	/*-------------------------------*/
	/*         Constructeur          */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
		$this->specialiteRepository = new SpecialiteRepository();
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
		if (empty($diplomes)) { return; }

		$taillePartie = 500;
		$total        = count($diplomes);

		for ($offset = 0; $offset < $total; $offset += $taillePartie)
		{
			$partie = array_slice($diplomes, $offset, $taillePartie);
			$this->insertChunk($partie);
		}
	}

	private function insertChunk(array $diplomes): void
	{
		if (empty($diplomes)) { return; }

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
				ON CONFLICT ON CONSTRAINT diplome_unique DO UPDATE SET
					diplome_type_code     = EXCLUDED.diplome_type_code,
					diplome_type_libelle  = EXCLUDED.diplome_type_libelle,
					diplome_serie_code    = EXCLUDED.diplome_serie_code,
					diplome_serie_libelle = EXCLUDED.diplome_serie_libelle,
					specialite_id         = EXCLUDED.specialite_id
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
		$specialite = $this->specialiteRepository->findById((int) $row['specialite_id']);

		return new Diplome
		(
			$row['diplome_id'           ],
			$row['diplome_type_code'    ],
			$row['diplome_type_libelle' ],
			$row['diplome_serie_code'   ],
			$row['diplome_serie_libelle'],
			$specialite,
			[]
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
