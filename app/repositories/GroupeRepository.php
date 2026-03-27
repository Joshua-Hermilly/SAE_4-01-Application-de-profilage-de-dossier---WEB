<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Groupe.php';
require_once '../app/repositories/CandidatRepository.php';
require_once '../app/repositories/CritereRepository.php';

class GroupeRepository
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
	public function create(Groupe $groupe)
	{
		$sql = "INSERT INTO GROUPE (groupe_nom, groupe_couleur, groupe_note_dossier)
				VALUES (:nom, :couleur, :note_dossier)
				RETURNING groupe_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':nom'          , $groupe->getGroupeNom          ());
		$stmt->bindValue(':couleur'      , $groupe->getGroupeCouleur      ());
		$stmt->bindValue(':note_dossier' , $groupe->getGroupeNoteDossier  ());

		if ($stmt->execute())
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$groupe->setGroupeId( $row['groupe_id'] );

		$stmt->execute();
	}

	public function update(Groupe $groupe): bool
	{
		$sql = "UPDATE GROUPE SET
				groupe_nom           = :nom,
				groupe_couleur       = :couleur,
				groupe_note_dossier  = :note_dossier
				WHERE groupe_id = :id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id'           , $groupe->getGroupeId           ());
		$stmt->bindValue(':nom'          , $groupe->getGroupeNom          ());
		$stmt->bindValue(':couleur'      , $groupe->getGroupeCouleur      ());
		$stmt->bindValue(':note_dossier' , $groupe->getGroupeNoteDossier  ());

		return $stmt->execute();
	}

	private function createGroupeFromRow(array $row): Groupe
	{
		$candidats = (new CandidatRepository())->findByGroupeId((int)$row['groupe_id']);
		$criteres  = (new CritereRepository ())->findByGroupeId((int)$row['groupe_id']);

		return new Groupe
		(
			(int)$row['groupe_id'],
			$row['groupe_nom'],
			$row['groupe_couleur'],
			$row['groupe_note_dossier'] !== null ? (float)$row['groupe_note_dossier'] : null,
			$criteres,
			$candidats
		);
	}

	public function findById(int $id): ?Groupe
	{
		$sql  = "SELECT * FROM GROUPE WHERE groupe_id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) { return $this->createGroupeFromRow($row); }

		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM GROUPE";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createGroupeFromRow($row);
		}
		return $result;
	}
}
