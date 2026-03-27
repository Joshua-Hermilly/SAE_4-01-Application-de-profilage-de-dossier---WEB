<?php

require_once '../app/entities/Compte.php';
require_once '../app/ore/Repository.php';

class CompteRepository
{
	/*╔════════════════════════╗*/
	/*║      Attributs         ║*/
	/*╚════════════════════════╝*/
	private $pdo;

	/*╔════════════════════════╗*/
	/*║     Constructeur       ║*/
	/*╚════════════════════════╝*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPdo();
	}

	/*╔════════════════════════╗*/
	/*║    Base de données     ║*/
	/*╚════════════════════════╝*/

	//Création
	public function create(Compte $compte): Compte
	{
		$sql = "INSERT INTO compte (compte_nom, compte_email, compte_mdp, compte_isadmin) 
		        VALUES (:compte_nom, :compte_email, :compte_mdp, :compte_isAdmin)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':compte_nom'    , $compte->getCompteNom    ());
		$stmt->bindValue(':compte_email'  , $compte->getCompteEmail  ());
		$stmt->bindValue(':compte_mdp'    , $compte->getCompteMdp    ());
		$stmt->bindValue(':compte_isAdmin', $compte->getCompteIsAdmin());
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row && isset($row['compte_id']))
		{
			$compte->setCompteId((int)$row['compte_id']);
		}
		return $compte;

	}

	public function createCompteFromRow(array $row): Compte
	{
		return new Compte
		(
			$row['compte_id'     ],
			$row['compte_nom'    ],
			$row['compte_email'  ],
			$row['compte_mdp'    ],
			$row['compte_isadmin']
		);
	}

	//Recherche
	public function findById(int $compte_id): ?Compte
	{
		$sql ="SELECT * FROM compte WHERE  compte_id = :compte_id";
		$stmt = $this->pdo->prepare($sql);

		$stmt->execute(['compte_id' => $compte_id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row ? $this->createCompteFromRow($row) : null;

	}

	public function findByNom(string $compte_nom): ?Compte
	{
		$sql ="SELECT * FROM compte WHERE  compte_nom = :compte_nom";
		$stmt = $this->pdo->prepare($sql);

		$stmt->execute(['compte_nom' => $compte_nom]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row ? $this->createCompteFromRow($row) : null;

	}

}
