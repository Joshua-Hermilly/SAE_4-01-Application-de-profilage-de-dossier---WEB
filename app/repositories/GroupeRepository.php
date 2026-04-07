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
		// La colonne groupe_id n'est pas en SERIAL dans le schéma, on calcule donc le prochain id manuellement
		$nextId = (int) $this->pdo->query('SELECT COALESCE(MAX(groupe_id), 0) + 1 FROM GROUPE')->fetchColumn();

		$sql = "INSERT INTO GROUPE (groupe_id, groupe_nom, groupe_couleur, groupe_note_dossier)
				VALUES (:id, :nom, :couleur, :note_dossier)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id'           , $nextId, PDO::PARAM_INT);
		$stmt->bindValue(':nom'          , $groupe->getGroupeNom          ());
		$stmt->bindValue(':couleur'      , $groupe->getGroupeCouleur      ());
		$stmt->bindValue(':note_dossier' , $groupe->getGroupeNoteDossier  ());

		$stmt->execute();
		$groupe->setGroupeId($nextId);
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

	private function buildFilteredQuery(array $filters, int $page, ?int $limit, bool $count = false): array
	{
		$select = $count
			? 'SELECT COUNT(DISTINCT G.groupe_id)'
			: 'SELECT G.*';

		$sql    = $select . " FROM GROUPE AS G\n";
		$joins  = [];
		$conds  = [];
		$params = [];

		if (!empty($filters['nom_groupe']))
		{
			$conds[] = 'G.groupe_nom = :nom_groupe';
			$params[':nom_groupe'] = $filters['nom_groupe'];
		}

		if (!empty($filters['critere']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F ON F.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C ON C.critere_id = F.critere_id";

			$values = is_array($filters['critere']) ? $filters['critere'] : [$filters['critere']];
			$placeholders = [];
			foreach ($values as $idx => $val)
			{
				$ph = ':critere_' . $idx;
				$placeholders[]      = $ph;
				$params[$ph] = $val;
			}
			if (!empty($placeholders))
			{
				$conds[] = 'C.critere_filtre IN (' . implode(', ', $placeholders) . ')';
			}
		}

		$minKey = 'note_dossier_min';
		$maxKey = 'note_dossier_max';
		if (isset($filters[$minKey]) && $filters[$minKey] !== '' && $filters[$minKey] !== null)
		{
			$conds[] = 'G.groupe_note_dossier >= :' . $minKey;
			$params[':' . $minKey] = (float) $filters[$minKey];
		}
		if (isset($filters[$maxKey]) && $filters[$maxKey] !== '' && $filters[$maxKey] !== null)
		{
			$conds[] = 'G.groupe_note_dossier <= :' . $maxKey;
			$params[':' . $maxKey] = (float) $filters[$maxKey];
		}

		if (!empty($joins))
		{
			$sql .= implode("\n", $joins) . "\n";
		}

		if (!empty($conds))
		{
			$sql .= 'WHERE ' . implode(' AND ', $conds) . "\n";
		}

		if ($count) { return [$sql, $params]; }

		$sql .= "ORDER BY G.groupe_nom, G.groupe_id\n";
		if ($limit !== null)
		{
			$sql .= "LIMIT :limit\nOFFSET :offset";
			$params[':offset'] = max(0, ($page - 1) * $limit);
			$params[':limit']  = $limit;
		}

		return [$sql, $params];
	}

	public function findByPage(int $page, array $filters = [], int $limit = 25): array
	{
		[$sql, $params] = $this->buildFilteredQuery($filters, $page, $limit, false);
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value)
		{
			$stmt->bindValue($key, $value);
		}
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createGroupeFromRow($row);
		}
		return $result;
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

	public function nbMaxDossier(array $filters = []): int
	{
		[$sql, $params] = $this->buildFilteredQuery($filters, 1, null, true);
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value)
		{
			$stmt->bindValue($key, $value);
		}
		$stmt->execute();
		return (int) $stmt->fetchColumn();
	}

	public function getDistinctNames(): array
	{
		$sql  = "SELECT DISTINCT groupe_nom FROM GROUPE WHERE groupe_nom IS NOT NULL AND TRIM(groupe_nom) <> '' ORDER BY groupe_nom";
		$stmt = $this->pdo->query($sql);
		$names = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$names[] = $row['groupe_nom'];
		}
		return $names;
	}

	public function supprimer(mixed $GroupeId): void
	{
		$sqlDelete = "DELETE FROM FILTRE WHERE groupe_id = :id";
		$stmtDelete = $this->pdo->prepare($sqlDelete);
		$stmtDelete->bindValue(':id', $GroupeId, PDO::PARAM_INT);
		$stmtDelete->execute();

		$sqlUpdate = "UPDATE CANDIDAT SET groupe_id = NULL WHERE groupe_id = :id";
		$stmtUpdate = $this->pdo->prepare($sqlUpdate);
		$stmtUpdate->bindValue(':id', $GroupeId, PDO::PARAM_INT);
		$stmtUpdate->execute();

		$sqlDelete = "DELETE FROM GROUPE WHERE groupe_id = :id";
		$stmtDelete = $this->pdo->prepare($sqlDelete);
		$stmtDelete->bindValue(':id', $GroupeId, PDO::PARAM_INT);
		$stmtDelete->execute();

	}
}
