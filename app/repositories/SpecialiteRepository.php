<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Specialite.php';

class SpecialiteRepository
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
	public function create(Specialite $specialite)
	{
		$sql = "INSERT INTO SPECIALITE 
    				(specialite_opt1, specialite_opt2, specialite_spe1, specialite_spe2, specialite_spe3, specialite_speabd, diplome_id)
		        VALUES 
		            (:opt1, :opt2, :spe1, :spe2,:spe3, :speAbd, :diplome_id )
		        RETURNING specialite_id;";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':opt1'      , $specialite->getSpecialiteOpt1  ());
		$stmt->bindValue(':opt2'      , $specialite->getSpecialiteOpt2  ());
		$stmt->bindValue(':spe1'      , $specialite->getSpecialiteSpe1  ());
		$stmt->bindValue(':spe2'      , $specialite->getSpecialiteSpe2  ());
		$stmt->bindValue(':spe3'      , $specialite->getSpecialiteSpe3  ());
		$stmt->bindValue(':speAbd'    , $specialite->getSpecialiteSpeAbd());
		$stmt->bindValue(':diplome_id', $specialite->getDiplomeId       ());
		$stmt->execute();

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$specialite->setSpecialiteId((int) $row['specialite_id'] );
	}


	public function creates(array $specialites): void
	{
		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($specialites); $cpt++)
		{
			$specialite = $specialites[$cpt];
			$valeurBind[] = "(:opt1_{$cpt}, :opt2_{$cpt}, :spe1_{$cpt}, :spe2_{$cpt}, :spe3_{$cpt}, :speAbd_{$cpt}, :diplome_id_{$cpt})";

			$valeurBrut[":opt1_{$cpt}"      ] = $specialite->getSpecialiteOpt1  ();
			$valeurBrut[":opt2_{$cpt}"      ] = $specialite->getSpecialiteOpt2  ();
			$valeurBrut[":spe1_{$cpt}"      ] = $specialite->getSpecialiteSpe1  ();
			$valeurBrut[":spe2_{$cpt}"      ] = $specialite->getSpecialiteSpe2  ();
			$valeurBrut[":spe3_{$cpt}"      ] = $specialite->getSpecialiteSpe3  ();
			$valeurBrut[":speAbd_{$cpt}"    ] = $specialite->getSpecialiteSpeAbd();
			$valeurBrut[":diplome_id_{$cpt}"] = $specialite->getDiplomeId       ();
		}

		$sql = "INSERT INTO SPECIALITE 
				(specialite_opt1, specialite_opt2, specialite_spe1, specialite_spe2, specialite_spe3, specialite_speabd, diplome_id)
				VALUES " . implode(', ', $valeurBind) . "
				RETURNING specialite_id";

		$stmt = $this->pdo->prepare($sql);

		for ($cpt = 0; $cpt < count($specialites); $cpt++)
		{
			$stmt->bindValue(":opt1_{$cpt}"      , $valeurBrut[":opt1_{$cpt}"      ]);
			$stmt->bindValue(":opt2_{$cpt}"      , $valeurBrut[":opt2_{$cpt}"      ]);
			$stmt->bindValue(":spe1_{$cpt}"      , $valeurBrut[":spe1_{$cpt}"      ]);
			$stmt->bindValue(":spe2_{$cpt}"      , $valeurBrut[":spe2_{$cpt}"      ]);
			$stmt->bindValue(":spe3_{$cpt}"      , $valeurBrut[":spe3_{$cpt}"      ]);
			$stmt->bindValue(":speAbd_{$cpt}"    , $valeurBrut[":speAbd_{$cpt}"    ]);
			$stmt->bindValue(":diplome_id_{$cpt}", $valeurBrut[":diplome_id_{$cpt}"]);
		}

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($cpt = 0; $cpt < count($specialites); $cpt++)
		{
			if (isset($rows[$cpt]))
			{
				$specialites[$cpt]->setSpecialiteId((int)$rows[$cpt]['specialite_id']);
			}
		}
	}

	public function createSpecialiteFromRow(array $row): Specialite
	{
		return new Specialite(
			(int)$row['specialite_id'    ],
			$row['specialite_opt1'  ],
			$row['specialite_opt2'  ],
			$row['specialite_spe1'  ],
			$row['specialite_spe2'  ],
			$row['specialite_spe3'  ],
			$row['specialite_speabd'],
			$row['diplome_id'       ]
		);
	}

	public function findById($id): ?Specialite
	{
		$sql = "SELECT * FROM specialite WHERE specialite_id = :id LIMIT 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute())
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row) { return $this->createSpecialiteFromRow($row); }
		}
		return null;
	}

	public function findAll()
	{
		$sql = "SELECT * FROM SPECIALITE";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createSpecialiteFromRow($row);
		}
		return $result;
	}

}
