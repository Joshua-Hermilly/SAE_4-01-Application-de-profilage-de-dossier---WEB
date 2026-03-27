<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Critere.php';

class CritereRepository
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
	private function createCritereFromRow($row)
    {
        return new Critere
		(
            (int) $row['critere_id'],
			$row['critere_libelle'],
			$row['critere_filtre' ],
			$row['critere_min'    ],
			$row['critere_max'    ]
        );
    }

	public function findById($id)
    {
		$stmt = $this->pdo->prepare("SELECT * FROM critere WHERE critere_id = :id");
		$stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) { return $this->createCritereFromRow($row); }

        return null;
    }

	public function create(Critere $critere)
	{
		$sql = "INSERT INTO critere (critere_libelle, critere_filtre, critere_min, critere_max)
				VALUES (:libelle, :filtre, :min, :max)";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':libelle', $critere->getCritereLibelle() );
		$req->bindValue(':filtre' , $critere->getCritereFiltre () );
		$req->bindValue(':min'    , $critere->getCritereMin    () );
		$req->bindValue(':max'    , $critere->getCritereMax    () );
		$req->execute();

		$critere->setCritereId( (int) $this->pdo->lastInsertId() );
	}

	public function update(Critere $critere)
    {
		$sql = "UPDATE critere SET
					critere_libelle = :libelle,
					critere_filtre  = :filtre,
					critere_min     = :min,
					critere_max     = :max
				WHERE critere_id = :id";
        $req = $this->pdo->prepare($sql);
		$req->bindValue(':id'     , $critere->getCritereId     () );
		$req->bindValue(':libelle', $critere->getCritereLibelle() );
		$req->bindValue(':filtre' , $critere->getCritereFiltre () );
		$req->bindValue(':min'    , $critere->getCritereMin    () );
		$req->bindValue(':max'    , $critere->getCritereMax    () );
        $req->execute();
    }

	public function findByGroupe(int $groupe_id): array
	{
		$sql = "SELECT c.*
				FROM critere c
				JOIN filtre f ON f.critere_id = c.critere_id
				WHERE f.groupe_id = :groupe_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':groupe_id', $groupe_id, PDO::PARAM_INT);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$items = [];
		foreach ($rows as $row)
		{
			$items[] = $this->createCritereFromRow($row);
		}

		return $items;
	}
}