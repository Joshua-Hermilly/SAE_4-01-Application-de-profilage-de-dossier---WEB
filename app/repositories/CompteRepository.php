<?php

require_once '../app/entities/Compte.php';
require_once '../app/core/Repository.php';

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
		$sql = "INSERT INTO compte (compte_identifiant, compte_mdp, compte_isadmin) 
		        VALUES (:compte_identifiant, :compte_mdp, :compte_isAdmin)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':compte_identifiant', $compte->getCompteIdentifiant());
		$stmt->bindValue(':compte_mdp'        , $compte->getCompteMdp        ());
		$stmt->bindValue(':compte_isAdmin'    , $compte->getCompteIsAdmin    (), PDO::PARAM_BOOL);
		$stmt->execute();

		return $compte;
	}

	public function createCompteFromRow(array $row): Compte
	{
		return new Compte
		(
			$row['compte_identifiant' ],
			$row['compte_mdp'         ],
			(string)$row['compte_isadmin']
		);
	}

	//Recherche
	public function findByIdentifiant(string $compte_identifiant): ?Compte
	{
		$sql ="SELECT * FROM compte WHERE  compte_identifiant = :compte_identifiant";
		$stmt = $this->pdo->prepare($sql);

		$stmt->execute(['compte_identifiant' => $compte_identifiant]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row ? $this->createCompteFromRow($row) : null;
	}
}
