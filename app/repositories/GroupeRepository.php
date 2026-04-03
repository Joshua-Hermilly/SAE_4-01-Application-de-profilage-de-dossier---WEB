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

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$groupe->setGroupeId( $row['groupe_id'] );
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

		// Filtre sur le nom du groupe
		if (!empty($filters['nom_groupe']))
		{
			$conds[] = 'G.groupe_nom = :nom_groupe';
			$params[':nom_groupe'] = $filters['nom_groupe'];
		}

		// Filtre générique sur les valeurs de critère (ancien filtre `critere`)
		if (!empty($filters['critere']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_critere ON F_critere.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_critere ON C_critere.critere_id = F_critere.critere_id";

			$values       = is_array($filters['critere']) ? $filters['critere'] : [$filters['critere']];
			$placeholders = [];
			foreach ($values as $idx => $val)
			{
				$ph              = ':critere_' . $idx;
				$placeholders[]  = $ph;
				$params[$ph]     = $val;
			}
			if (!empty($placeholders))
			{
				$conds[] = 'C_critere.critere_filtre IN (' . implode(', ', $placeholders) . ')';
			}
		}

		// Filtres de type "valeur exacte" basés sur les entités CRITERE
		if (!empty($filters['civilite']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_civilite ON F_civilite.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_civilite ON C_civilite.critere_id = F_civilite.critere_id";
			$conds[] = 'C_civilite.critere_libelle = :lib_civilite';
			$conds[] = 'C_civilite.critere_filtre  = :val_civilite';
			$params[':lib_civilite'] = 'civilite';
			$params[':val_civilite'] = $filters['civilite'];
		}

		if (isset($filters['boursier']) && ($filters['boursier'] !== '' || $filters['boursier'] === '0'))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_boursier ON F_boursier.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_boursier ON C_boursier.critere_id = F_boursier.critere_id";
			$conds[] = 'C_boursier.critere_libelle = :lib_boursier';
			$conds[] = 'C_boursier.critere_filtre  = :val_boursier';
			$params[':lib_boursier'] = 'boursier';
			$params[':val_boursier'] = (string) $filters['boursier'];
		}

		if (!empty($filters['profil']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_profil ON F_profil.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_profil ON C_profil.critere_id = F_profil.critere_id";
			$conds[] = 'C_profil.critere_libelle = :lib_profil';
			$conds[] = 'C_profil.critere_filtre ILIKE :val_profil';
			$params[':lib_profil'] = 'profil';
			$params[':val_profil'] = '%' . $filters['profil'] . '%';
		}

		if (!empty($filters['type_bac']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_type_bac ON F_type_bac.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_type_bac ON C_type_bac.critere_id = F_type_bac.critere_id";
			$conds[] = 'C_type_bac.critere_libelle = :lib_type_bac';
			$conds[] = 'C_type_bac.critere_filtre  = :val_type_bac';
			$params[':lib_type_bac'] = 'type_bac';
			$params[':val_type_bac'] = $filters['type_bac'];
		}

		if (!empty($filters['serie_bac']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_serie_bac ON F_serie_bac.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_serie_bac ON C_serie_bac.critere_id = F_serie_bac.critere_id";
			$conds[] = 'C_serie_bac.critere_libelle = :lib_serie_bac';
			$conds[] = 'C_serie_bac.critere_filtre  = :val_serie_bac';
			$params[':lib_serie_bac'] = 'serie_bac';
			$params[':val_serie_bac'] = $filters['serie_bac'];
		}

		if (!empty($filters['specialite_spe']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_specialite_spe ON F_specialite_spe.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_specialite_spe ON C_specialite_spe.critere_id = F_specialite_spe.critere_id";
			$values = is_array($filters['specialite_spe']) ? $filters['specialite_spe'] : [$filters['specialite_spe']];
			$placeholders = [];
			foreach ($values as $idx => $val)
			{
				$ph              = ':spe_' . $idx;
				$placeholders[]  = $ph;
				$params[$ph]     = $val;
			}
			if (!empty($placeholders))
			{
				$conds[] = 'C_specialite_spe.critere_libelle = :lib_specialite_spe';
				$conds[] = 'C_specialite_spe.critere_filtre IN (' . implode(', ', $placeholders) . ')';
				$params[':lib_specialite_spe'] = 'specialite_spe';
			}
		}

		if (!empty($filters['specialite_opt']))
		{
			$joins[] = "LEFT JOIN FILTRE  AS F_specialite_opt ON F_specialite_opt.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS C_specialite_opt ON C_specialite_opt.critere_id = F_specialite_opt.critere_id";
			$values = is_array($filters['specialite_opt']) ? $filters['specialite_opt'] : [$filters['specialite_opt']];
			$placeholders = [];
			foreach ($values as $idx => $val)
			{
				$ph              = ':opt_' . $idx;
				$placeholders[]  = $ph;
				$params[$ph]     = $val;
			}
			if (!empty($placeholders))
			{
				$conds[] = 'C_specialite_opt.critere_libelle = :lib_specialite_opt';
				$conds[] = 'C_specialite_opt.critere_filtre IN (' . implode(', ', $placeholders) . ')';
				$params[':lib_specialite_opt'] = 'specialite_opt';
			}
		}

		// Filtres de notes (valeurs comprises entre critere_min et critere_max)
		foreach (['note_lycee', 'note_fiche', 'note_globale'] as $noteKey)
		{
			$minKey = $noteKey . '_min';
			$maxKey = $noteKey . '_max';
			$hasMin = isset($filters[$minKey]) && $filters[$minKey] !== '' && $filters[$minKey] !== null;
			$hasMax = isset($filters[$maxKey]) && $filters[$maxKey] !== '' && $filters[$maxKey] !== null;

			if (!$hasMin && !$hasMax) { continue; }

			$aliasF = 'F_' . $noteKey;
			$aliasC = 'C_' . $noteKey;
			$joins[] = "LEFT JOIN FILTRE  AS $aliasF ON $aliasF.groupe_id  = G.groupe_id";
			$joins[] = "LEFT JOIN CRITERE AS $aliasC ON $aliasC.critere_id = $aliasF.critere_id";
			$conds[] = "$aliasC.critere_libelle = :lib_{$noteKey}";
			$params[":lib_{$noteKey}"] = $noteKey;

			if ($hasMin)
			{
				$params[":{$minKey}"] = (float) $filters[$minKey];
				$conds[] = "$aliasC.critere_min <= :{$minKey}";
				$conds[] = "(:{$minKey} <= $aliasC.critere_max OR $aliasC.critere_max IS NULL)";
			}
			if ($hasMax)
			{
				$params[":{$maxKey}"] = (float) $filters[$maxKey];
				$conds[] = "$aliasC.critere_min <= :{$maxKey}";
				$conds[] = "(:{$maxKey} <= $aliasC.critere_max OR $aliasC.critere_max IS NULL)";
			}
		}

		// Filtre sur la note de dossier du groupe (colonne directe)
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

		if (!empty($joins)) { $sql .=            implode("\n", $joins   ) . "\n"; }

		if (!empty($conds)) { $sql .= 'WHERE ' . implode(' AND ', $conds) . "\n"; }

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
		foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $result[] = $this->createGroupeFromRow($row); }
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
		$sql = "SELECT * FROM GROUPE ORDER BY groupe_id DESC, groupe_nom DESC";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $result[] = $this->createGroupeFromRow($row); }
		return $result;
	}

	public function nbMaxDossier(array $filters = []): int
	{
		[$sql, $params] = $this->buildFilteredQuery($filters, 1, null, true);
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
		$stmt->execute();
		return (int) $stmt->fetchColumn();
	}

	public function getDistinctNames(): array
	{
		$sql   = "SELECT DISTINCT groupe_nom FROM GROUPE WHERE groupe_nom IS NOT NULL AND TRIM(groupe_nom) <> '' ORDER BY groupe_nom";
		$stmt  = $this->pdo->query($sql);
		$names = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $names[] = $row['groupe_nom']; }
		return $names;
	}
}
