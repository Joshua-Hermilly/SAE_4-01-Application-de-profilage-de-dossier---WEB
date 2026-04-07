<?php
require_once '../app/core/Repository.php';
require_once '../app/entities/DossierCandidat.php';

class DossierCandidatRepository
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
	public function createDossierCandidatFromRow(array $row): DossierCandidat
	{
		return new DossierCandidat
		(
			$row['candidat_code'],
			$row['candidat_civilite'],
			$row['candidat_boursier_code'],
			round($row['candidat_note_lycee'  ] ?? -1, 2),
			round($row['candidat_note_fiche'  ] ?? -1, 2),
			round($row['candidat_note_globale'] ?? -1, 2),
			$row['etablissement_nom'],
			$row['diplome_serie_code'],
			$row['specialite_spe1'],
			$row['specialite_spe2'],
			$row['groupe_couleur'],
		);
	}

	public function findAll(): array
	{
		[$sql, $params] = $this->buildFilteredQuery([], 1, null, false);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $result[] = $this->createDossierCandidatFromRow($row); }
		return $result;
	}

	public function findByPage(int $page, array $filters = [], int $limit = 25): array
	{
		[$sql, $params] = $this->buildFilteredQuery($filters, $page, $limit, false);
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $result[] = $this->createDossierCandidatFromRow($row); }
		return $result;
	}

	public function findByFilters(array $filters, int $page = 1): array { return $this->findByPage($page, $filters); }

	public function nbMaxDossier(array $filters = []): int
	{
		[$sql, $params] = $this->buildFilteredQuery($filters, 1, 1, true);
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
		$stmt->execute();
		return (int) $stmt->fetchColumn();
	}

	private function buildFilteredQuery(array $filters, int $page, ?int $limit, bool $count = false): array
	{
		$select = $count
			? 'SELECT COUNT(*)'
			: 'SELECT C.candidat_code, C.candidat_civilite, C.candidat_boursier_code, C.candidat_note_lycee, C.candidat_note_fiche, C.candidat_note_globale, E.etablissement_nom, D.diplome_serie_code, S.specialite_spe1, S.specialite_spe2, G.groupe_couleur';

		$sql = $select . " FROM CANDIDAT AS C\n"
			           . "LEFT JOIN ETABLISSEMENT AS E ON E.etablissement_id = C.etablissement_id\n"
			           . "LEFT JOIN DIPLOME       AS D ON D.diplome_id       = C.diplome_id      \n"
			           . "LEFT JOIN SPECIALITE    AS S ON S.specialite_id    = D.specialite_id   \n"
			           . "LEFT JOIN GROUPE        AS G ON G.groupe_id        = C.groupe_id       \n";

		$conditions = [];
		$params = [];

		if (!empty($filters['civilite']))
		{
			$conditions[] = 'C.candidat_civilite = :civilite';
			$params[':civilite'] = $filters['civilite'];
		}

		if (isset($filters['boursier']) && ($filters['boursier'] !== '' || $filters['boursier'] === '0'))
		{
			$conditions[] = 'C.candidat_boursier_code = :boursier';
			$params[':boursier'] = (int) $filters['boursier'];
		}

		if (!empty($filters['profil']))
		{
			$conditions[] = 'C.candidat_profil ILIKE :profil';
			$params[':profil'] = '%' . $filters['profil'] . '%';
		}

		if (!empty($filters['type_bac']))
		{
			$conditions[] = 'D.diplome_type_libelle = :type_bac';
			$params[':type_bac'] = $filters['type_bac'];
		}

		if (!empty($filters['serie_bac']))
		{
			$conditions[] = 'D.diplome_serie_code = :serie_bac';
			$params[':serie_bac'] = $filters['serie_bac'];
		}

		if (!empty($filters['specialite_spe']))
		{
			$specialites = is_array($filters['specialite_spe']) ? $filters['specialite_spe'] : [$filters['specialite_spe']];
			$specialiteParts = [];
			foreach ($specialites as $index => $specialite)
			{
				$param = ':specialite_' . $index;
				$specialiteParts[] = "(COALESCE(S.specialite_opt1, '') = $param OR COALESCE(S.specialite_opt2, '') = $param OR COALESCE(S.specialite_spe1, '') = $param OR COALESCE(S.specialite_spe2, '') = $param OR COALESCE(S.specialite_spe3, '') = $param OR COALESCE(S.specialite_speabd, '') = $param)";
				$params[$param] = $specialite;
			}
			$conditions[] = '(' . implode(' OR ', $specialiteParts) . ')';
		}

		if (!empty($filters['specialite_opt']))
		{
			$options = is_array($filters['specialite_opt']) ? $filters['specialite_opt'] : [$filters['specialite_opt']];
			$optionParts = [];
			foreach ($options as $index => $option)
			{
				$param = ':option_' . $index;
				$optionParts[] = "(COALESCE(S.specialite_opt1, '') = $param OR COALESCE(S.specialite_opt2, '') = $param)";
				$params[$param] = $option;
			}
			$conditions[] = '(' . implode(' OR ', $optionParts) . ')';
		}

		foreach (['note_lycee', 'note_fiche', 'note_globale'] as $noteFilter)
		{
			$minKey = $noteFilter . '_min';
			$maxKey = $noteFilter . '_max';
			if (isset($filters[$minKey]) && $filters[$minKey] !== '' && $filters[$minKey] !== null)
			{
				$conditions[] = 'C.candidat_' . $noteFilter . ' >= :' . $minKey;
				$params[':' . $minKey] = (float) $filters[$minKey];
			}
			if (isset($filters[$maxKey]) && $filters[$maxKey] !== '' && $filters[$maxKey] !== null)
			{
				$conditions[] = 'C.candidat_' . $noteFilter . ' <= :' . $maxKey;
				$params[':' . $maxKey] = (float) $filters[$maxKey];
			}
		}

		if (!empty($conditions)) { $sql .= 'WHERE ' . implode(' AND ', $conditions) . "\n"; }

		if ($count) { return [$sql, $params]; }

		$sql .= "ORDER BY C.candidat_code\n";
		if ($limit !== null)
		{
			$sql .= "LIMIT :limit\nOFFSET :offset";
			$params[':offset'] = max(0, ($page - 1) * $limit);
			$params[':limit'] = $limit;
		}

		return [$sql, $params];
	}
}