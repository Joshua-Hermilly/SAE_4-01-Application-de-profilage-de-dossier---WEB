<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/repositories/CandidatRepository.php';

class EtablissementRepository
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

	// CREATE
	// - - - - - - -
	private function createEtablissementFromRow(array $row): Etablissement
	{
		return new Etablissement
		(
			(int  )$row['etablissement_id'          ],
			       $row['etablissement_nom'         ],
			       $row['etablissement_pays'        ],
			       $row['etablissement_code_postal' ],
			       $row['etablissement_commune'     ],
			       $row['etablissement_departement' ],
			(float)$row['etablissement_latitude'    ],
			(float)$row['etablissement_longitude'   ],
			(float)$row['etablissement_distance'    ]
		);
	}

	public function create(Etablissement $etablissement): void
	{
		$sql = "INSERT INTO ETABLISSEMENT
				(etablissement_nom, etablissement_pays, etablissement_code_postal, etablissement_commune, etablissement_departement, etablissement_latitude, etablissement_longitude, etablissement_distance)
				VALUES (:nom, :pays, :codePostal, :commune, :departement, :latitude, :longitude, :distance)
				RETURNING etablissement_id";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':nom'        , $etablissement->getEtablissementNom        ());
		$req->bindValue(':pays'       , $etablissement->getEtablissementPays       ());
		$req->bindValue(':codePostal' , $etablissement->getEtablissementCodePostal ());
		$req->bindValue(':commune'    , $etablissement->getEtablissementCommune    ());
		$req->bindValue(':departement', $etablissement->getEtablissementDepartement());
		$req->bindValue(':latitude'   , $etablissement->getEtablissementLatitude   ());
		$req->bindValue(':longitude'  , $etablissement->getEtablissementLongitude  ());
		$req->bindValue('distance'    , $etablissement->getEtablissementDistance   ());

		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		$etablissement->setEtablissementId( (int) $row['etablissement_id'] );
	}

public function creates(array $etablissements): void
{
	$valeurBind = [];
	$values = [];

	for ($cpt=0; $cpt<count($etablissements); $cpt++)
	{
		$etablissement = $etablissements[$cpt];
		$values[]  = "(:nom{$cpt}, :pays{$cpt}, :codePostal{$cpt}, :commune{$cpt}, :departement{$cpt}, :latitude{$cpt}, :longitude{$cpt}, :distance{$cpt})";

		$valeurBind[":nom{$cpt}"]         = $etablissement->getEtablissementNom        ();
		$valeurBind[":pays{$cpt}"]        = $etablissement->getEtablissementPays       ();
		$valeurBind[":codePostal{$cpt}"]  = $etablissement->getEtablissementCodePostal ();
		$valeurBind[":commune{$cpt}"]     = $etablissement->getEtablissementCommune    ();
		$valeurBind[":departement{$cpt}"] = $etablissement->getEtablissementDepartement();
		$valeurBind[":latitude{$cpt}"]    = $etablissement->getEtablissementLatitude   ();
		$valeurBind[":longitude{$cpt}"]   = $etablissement->getEtablissementLongitude  ();
		$valeurBind[":distance{$cpt}"]    = $etablissement->getEtablissementDistance   ();
	}

	$sql = "INSERT INTO ETABLISSEMENT
			(etablissement_nom, etablissement_pays, etablissement_code_postal, etablissement_commune, etablissement_departement, etablissement_latitude, etablissement_longitude, etablissement_distance)
			VALUES " . implode(', ', $values) . "
			RETURNING etablissement_id";

	$stmt = $this->pdo->prepare($sql);
	foreach ($valeurBind as $key => $value)
	{
		$stmt->bindValue($key, $value);
	}
	$stmt->execute();

	$cpt = 0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$etablissements[$cpt]->setEtablissementId((int) $row['etablissement_id']);
		$cpt++;
	}
}


	// UPDATE
	// - - - - - - -
	public function update($etablissement)
	{
		$sql = "UPDATE ETABLISSEMENT SET
		        	etablissement_nom         = :nom,
		        	etablissement_pays        = :pays,
		        	etablissement_code_postal = :codePostal,
		        	etablissement_commune     = :commune,
		        	etablissement_departement = :departement,
		        	etablissement_latitude    = :latitude,
		        	etablissement_longitude   = :longitude,
		        	etablissement_distance    = :distance
		        WHERE etablissement_id = :id";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':id'         , $etablissement->getEtablissementId         ());
		$req->bindValue(':nom'        , $etablissement->getEtablissementNom        ());
		$req->bindValue(':pays'       , $etablissement->getEtablissementPays       ());
		$req->bindValue(':codePostal' , $etablissement->getEtablissementCodePostal ());
		$req->bindValue(':commune'    , $etablissement->getEtablissementCommune    ());
		$req->bindValue(':departement', $etablissement->getEtablissementDepartement());
		$req->bindValue(':latitude'   , $etablissement->getEtablissementLatitude   ());
		$req->bindValue(':longitude'  , $etablissement->getEtablissementLongitude  ());
		$req->bindValue('distance'    , $etablissement->getEtablissementDistance   ());
		$req->execute();
	}

	// FIND
	// - - - - - - -
	public function findById(int $id)
	{
		$sql = "SELECT * FROM etablissement WHERE id = :id LIMIT 1";
		$req = $this->pdo->prepare($sql);
		$req->bindValue(":id", $id);
		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createEtablissementFromRow($row); }
		return null;
	}

	public function findAll()
	{
		$sql = "SELECT * FROM etablissement";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}

	public function findByDistance($distance)
	{
		$sql = "SELECT * FROM ETABLISEEMENT WHERE distance <= :distance";
		$sql = $this->pdo->prepare($sql);
		$sql->bindValue(':distance', $distance);
		$sql->execute();

		$result = [];
		while ($row = $sql->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}

	public function findMaxDistance()
	{
		$sql = "SELECT MAX(etablissement_distance) FROM ETABLISEEMENT";
		$req = $this->pdo->prepare($sql);
		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createEtablissementFromRow($row); }
		return null;
	}

	public function updatePos(Etablissement $etablissement)
	{
		$sql = "SELECT etablissement_id, etablissement_pays
		        FROM   ETABLISSEMENT
		        WHERE  etablissement_nom         = :nom        AND
		               etablissement_code_postal = :codePostal AND
		               etablissement_commune     = :commune";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':nom'        , $etablissement->getEtablissementNom       ());
		$req->bindValue(':codePostal' , $etablissement->getEtablissementCodePostal());
		$req->bindValue(':commune'    , $etablissement->getEtablissementCommune   ());

		$req->execute();
		$row = $req->fetch(PDO::FETCH_ASSOC);
		if ( $row )
		{
			$etablissement->setEtablissementId  ((int) $row['etablissement_id'  ]);
			$etablissement->setEtablissementPays(      $row['etablissement_pays']);
			$this        ->update               ($etablissement                  );
		}
	}
}
