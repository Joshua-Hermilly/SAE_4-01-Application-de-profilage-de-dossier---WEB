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
        $stmt = $this->pdo->prepare("SELECT * FROM CRITERE WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) { return $this->createCritereFromRow($row); }

        return null;
    }

	public function create(Critere $critere)
	{
		$sql = "INSERT INTO CRITERE (critere_libelle, critere_filtre, critere_min, critere_max)
				VALUES (:libelle, :filtre, :min, :max)";

		$req = $this->pdo->prepare($sql);
		$req->bindValue(':libelle', $critere->getCritereLibelle() );
		$req->bindValue(':filtre' , $critere->getCritereFiltre () );
		$req->bindValue(':min'    , $critere->getCritereMin    () );
		$req->bindValue(':max'    , $critere->getCritereMax    () );
		$req->execute();

		$row = $req->fetch(PDO::FETCH_ASSOC);
		$critere->setCritereId( (int) $row['critere_id'] );
	}

	public function update(Critere $critere)
    {
        $sql = "UPDATE CRITERE SET
                    critere_id      = :series_id,
                    critere_libelle = :title,
                    critere_filtre  = :season,
                    critere_min     = :episode_number,
                    critere_max     = :duration
                WHERE id = :id";
        $req = $this->pdo->prepare($sql);
        $req->bindValue(':critere_id', $critere->getCritereId     () );
		$req->bindValue(':libelle'   , $critere->getCritereLibelle() );
		$req->bindValue(':filtre'    , $critere->getCritereFiltre () );
		$req->bindValue(':min'       , $critere->getCritereMin    () );
		$req->bindValue(':max'       , $critere->getCritereMax    () );
        $req->execute();
    }
}