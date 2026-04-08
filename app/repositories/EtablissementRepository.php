<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Etablissement.php';

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
		$nb = null;
		if (array_key_exists('nb_candidats', $row)) { $nb = (int)$row['nb_candidats']; }
		return new Etablissement
		(
			(int)$row['etablissement_id'         ],
			     $row['etablissement_nom'        ],
			     $row['etablissement_pays'       ],
			     $row['etablissement_code_postal'],
			     $row['etablissement_commune'    ],
			     $row['etablissement_departement'],
			$row['etablissement_latitude' ] !== null ? (float)$row['etablissement_latitude' ] : null,
			$row['etablissement_longitude'] !== null ? (float)$row['etablissement_longitude'] : null,
			$row['etablissement_distance' ] !== null ? (float)$row['etablissement_distance' ] : null,
			$nb
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
		$req->bindValue(':distance'   , $etablissement->getEtablissementDistance   ());

		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		$etablissement->setEtablissementId( (int) $row['etablissement_id'] );
	}

public function creates(array $etablissements): void
{
	if (empty($etablissements)) { return; }

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

	try {
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
	} catch (PDOException $e) {
		// En cas d'erreur (ex: doublon), insérer un par un
		foreach ($etablissements as $etablissement) {
			try {
				$this->create($etablissement);
			} catch (PDOException $ex) {
				// Ignorer les doublons
			}
		}
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
		$req->bindValue(':distance'   , $etablissement->getEtablissementDistance   ());
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
		$sql = "SELECT e.*, COUNT(c.candidat_code) AS nb_candidats
				FROM ETABLISSEMENT e
				LEFT JOIN CANDIDAT c ON c.etablissement_id = e.etablissement_id
				GROUP BY e.etablissement_id,
						 e.etablissement_nom,
						 e.etablissement_pays,
						 e.etablissement_code_postal,
						 e.etablissement_commune,
						 e.etablissement_departement,
						 e.etablissement_latitude,
						 e.etablissement_longitude,
						 e.etablissement_distance";
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
		$sql = "SELECT e.*, COUNT(c.candidat_code) AS nb_candidats
				FROM ETABLISSEMENT e
				LEFT JOIN CANDIDAT c ON c.etablissement_id = e.etablissement_id
				WHERE e.etablissement_distance <= :distance
				GROUP BY e.etablissement_id,
						 e.etablissement_nom,
						 e.etablissement_pays,
						 e.etablissement_code_postal,
						 e.etablissement_commune,
						 e.etablissement_departement,
						 e.etablissement_latitude,
						 e.etablissement_longitude,
						 e.etablissement_distance";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':distance', $distance);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createEtablissementFromRow($row);
		}
		return $result;
	}

	public function findMaxDistance()
	{
		$sql = "SELECT MAX(etablissement_distance) AS max_distance FROM ETABLISSEMENT";
		$req = $this->pdo->prepare($sql);
		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);

		if ($row && $row['max_distance'] !== null) {
			return (float) $row['max_distance'];
		}

		return 0;
	}

	public function updatePos(Etablissement $etablissement)
	{
		$sql = "SELECT etablissement_id
				FROM   ETABLISSEMENT
				WHERE  etablissement_nom         IS NOT DISTINCT FROM :nom        AND
					   etablissement_code_postal IS NOT DISTINCT FROM :codePostal AND
					   etablissement_pays        IS NOT DISTINCT FROM :pays       AND
					   etablissement_commune     IS NOT DISTINCT FROM :commune";

		$req = $this->pdo->prepare($sql);

		// On s'assure que si la chaîne est vide, on la passe en tant que null pour correspondre à la BDD
		$req->bindValue(':nom'        , $etablissement->getEtablissementNom()        === '' ? null : $etablissement->getEtablissementNom());
		$req->bindValue(':codePostal' , $etablissement->getEtablissementCodePostal() === '' ? null : $etablissement->getEtablissementCodePostal());
		$req->bindValue(':commune'    , $etablissement->getEtablissementCommune()    === '' ? null : $etablissement->getEtablissementCommune());
		$req->bindValue(':pays'       , $etablissement->getEtablissementPays()       === '' ? null : $etablissement->getEtablissementPays());

		$req->execute();
		$row = $req->fetch(PDO::FETCH_ASSOC);

		if ($row)
		{
			$this->updatePositions(
				(int) $row['etablissement_id'],
				$etablissement->getEtablissementLatitude(),
				$etablissement->getEtablissementLongitude(),
				$etablissement->getEtablissementDistance()
			);
		}
	}

	public function updatePositions(int $id, ?float $latitude, ?float $longitude, ?float $distance): void
	{
		$sql = "UPDATE ETABLISSEMENT 
				SET etablissement_latitude  = :latitude,
					etablissement_longitude = :longitude,
					etablissement_distance  = :distance
				WHERE etablissement_id = :id";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':id'       , $id);
		$req->bindValue(':latitude' , $latitude);
		$req->bindValue(':longitude', $longitude);
		$req->bindValue(':distance' , $distance);
		$req->execute();
	}


}
