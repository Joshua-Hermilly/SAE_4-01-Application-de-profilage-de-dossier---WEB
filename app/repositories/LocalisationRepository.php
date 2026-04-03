<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Localisation.php';

class LocalisationRepository
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*  Requetes                     */
	/*-------------------------------*/
	private function createLocalisationFromRow(array $row): Localisation
	{
		return new Localisation
		(
			(int) $row['localisation_id'],
			$row['localisation_pays'       ],
			$row['localisation_code_postal'],
			$row['localisation_commune'    ],
			$row['localisation_departement'],
			$row['localisation_latitide'   ],
			$row['localisation_longitude'  ],
			$row['localisation_distance'   ]
		);
	}

	public function create(Localisation $localisation): void
	{
		$sql = "INSERT INTO LOCALISATION
				(localisation_pays, localisation_code_postal, localisation_commune, localisation_departement, localisation_latitide, localisation_longitude, localisation_distance)
				VALUES (:pays, :code_postal, :commune, :departement, :latitde, :longitude, :dis)";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':pays'       , $localisation->getLocalisationPays       ());
		$req->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
		$req->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
		$req->bindValue(':departement', $localisation->getLocalisationDepartement());
		$req->bindValue(':latitde'    , $localisation->getLocalisationLatitude   ());
		$req->bindValue(':longitude'  , $localisation->getLocalisationLongitude  ());
		$req->bindValue(':dis'        , $localisation->getLocalisationDistance   ());
		$req->execute();

		$localisation->setLocalisationId((int) $this->pdo->lastInsertId());
	}

		public function creates(array $localisations): void
	{
		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($localisations); $cpt++)
		{
			$localisation = $localisations[$cpt];
			$valeurBind[]  = "(:pays{$cpt}, :code_postal{$cpt}, :commune{$cpt}, :departement{$cpt}, :latitde{$cpt}, :longitude{$cpt}, :dis{$cpt})";

			$valeurBrut[":pays{$cpt}"       ] = $localisation->getLocalisationPays       ();
			$valeurBrut[":code_postal{$cpt}"] = $localisation->getLocalisationCodePostal ();
			$valeurBrut[":commune{$cpt}"    ] = $localisation->getLocalisationCommune    ();
			$valeurBrut[":departement{$cpt}"] = $localisation->getLocalisationDepartement();
			$valeurBrut[":latitde{$cpt}"    ] = $localisation->getLocalisationLatitude   ();
			$valeurBrut[":longitude{$cpt}"  ] = $localisation->getLocalisationLongitude  ();
			$valeurBrut[":dis{$cpt}"        ] = $localisation->getLocalisationDistance   ();
		}

		$sql = "INSERT INTO LOCALISATION 
				(localisation_pays, localisation_code_postal, localisation_commune, localisation_departement, localisation_latitide, localisation_longitude, localisation_distance)
				VALUES " . implode(', ', $valeurBind) . "
				RETURNING localisation_id";

		$stmt = $this->pdo->prepare($sql);

		for ($cpt = 0; $cpt < count($localisations); $cpt++)
		{
			$stmt->bindValue(":pays{$cpt}"       , $valeurBrut[":pays{$cpt}"       ]);
			$stmt->bindValue(":code_postal{$cpt}", $valeurBrut[":code_postal{$cpt}"]);
			$stmt->bindValue(":commune{$cpt}"    , $valeurBrut[":commune{$cpt}"    ]);
			$stmt->bindValue(":departement{$cpt}", $valeurBrut[":departement{$cpt}"]);
			$stmt->bindValue(":latitde{$cpt}"    , $valeurBrut[":latitde{$cpt}"    ]);
			$stmt->bindValue(":longitude{$cpt}"  , $valeurBrut[":longitude{$cpt}"  ]);
			$stmt->bindValue(":dis{$cpt}"        , $valeurBrut[":dis{$cpt}"        ]);
		}

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($cpt = 0; $cpt < count($localisations); $cpt++)
		{
			if (isset($rows[$cpt]))
			{
				$localisations[$cpt]->setLocalisationId((int) $rows[$cpt]['localisation_id']);
			}
		}
	}

	public function update(Localisation $localisation)
	{
		$sql = "UPDATE LOCALISATION
				SET localisation_latitide    = :latitde,
					localisation_longitude   = :longitude,
					localisation_distance    = :dis
				WHERE localisation_id = :id";


		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id'         , $localisation->getLocalisationId         ());
		$stmt->bindValue(':latitde'    , $localisation->getLocalisationLatitude   ());
		$stmt->bindValue(':longitude'  , $localisation->getLocalisationLongitude  ());
		$stmt->bindValue(':dis'        , $localisation->getLocalisationDistance   ());
		$stmt->execute();
	}

	public function findById(int $id): ?Localisation
	{
		$sql  = "SELECT * FROM localisation WHERE localisation_id = :id LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createLocalisationFromRow($row); }

		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM LOCALISATION";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createLocalisationFromRow($row);
		}
		return $result;
	}

	public function exist(Localisation $localisation): int
	{
		$sql  = "SELECT * FROM localisation 
                 WHERE
				       localisation_code_postal = :code_postal
				   AND localisation_commune     = :commune
				   AND localisation_departement = :departement
				 LIMIT 1";

		$stmt = $this->pdo->prepare($sql);
		//$stmt->bindValue(':pays'       , $localisation->getLocalisationPays       ());
		$stmt->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
		$stmt->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
		$stmt->bindValue(':departement', $localisation->getLocalisationDepartement());

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $row['localisation_id']; }
		return -1;
	}


	public function getMaxDistance()
	{
		$sql = "SELECT MAX(localisation_distance) FROM LOCALISATION";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['MAX(localisation_distance)'];
	}
}
