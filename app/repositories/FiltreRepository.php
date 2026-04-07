<?php

require_once '../app/core/Repository.php';

class FiltreRepository
{
	private $pdo;

	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	public function getOptionQueries(): array
	{
		return [
			'annee'           => [
				'sql'   => $this->selectDistinct('CANDIDAT', 'candidat_annee'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'civilite'        => [
				'sql'   => $this->selectDistinct('CANDIDAT', 'candidat_civilite'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'boursier'        => [
				'sql'   => $this->selectDistinct('CANDIDAT', 'candidat_boursier_code'),
				'label' => static function (array $row): ?string
				{
					if (!isset($row['val'])) { return null; }
					return match ((string) $row['val'])
					{
						'0' => 'Non boursier',
						default => 'Boursier (' . $row['val'] . ')',
					};
				},
			],
			'profil'          => [
				'sql'   => $this->selectDistinct('CANDIDAT', 'candidat_profil'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'type_bac'        => [
				'sql'   => $this->selectDistinct('DIPLOME', 'diplome_type_libelle'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'serie_bac'       => [
				'sql'   => $this->selectDistinct('DIPLOME', 'diplome_serie_code'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_spe' => [
				'sql'   => $this->unionDistinctWithSerieCode(),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_opt' => [
				'sql'   => $this->unionDistinctOptionsWithSerieCode(),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'nom_groupe'      => [
				'sql'   => $this->selectDistinct('GROUPE', 'groupe_nom'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
		];
	}

	private function selectDistinct(string $table, string $column): string
	{
		return "SELECT DISTINCT CAST($column AS TEXT) AS val FROM $table WHERE $column IS NOT NULL ORDER BY CAST($column AS TEXT)";
	}

	private function unionDistinctWithSerieCode(): string
	{
		return "
			SELECT DISTINCT 
				TRIM(SPECIALITE.specialite_spe1) AS val,
				DIPLOME.diplome_serie_code AS code
			FROM SPECIALITE
			JOIN DIPLOME ON SPECIALITE.specialite_id = DIPLOME.specialite_id
			WHERE TRIM(SPECIALITE.specialite_spe1) IS NOT NULL AND TRIM(SPECIALITE.specialite_spe1) <> ''
			UNION ALL
			SELECT DISTINCT 
				TRIM(SPECIALITE.specialite_spe2) AS val,
				DIPLOME.diplome_serie_code AS code
			FROM SPECIALITE
			JOIN DIPLOME ON SPECIALITE.specialite_id = DIPLOME.specialite_id
			WHERE TRIM(SPECIALITE.specialite_spe2) IS NOT NULL AND TRIM(SPECIALITE.specialite_spe2) <> ''
			ORDER BY val, code
		";
	}

	private function unionDistinctOptionsWithSerieCode(): string
	{
		return "
			SELECT DISTINCT 
				TRIM(SPECIALITE.specialite_opt1) AS val,
				DIPLOME.diplome_serie_code AS code
			FROM SPECIALITE
			JOIN DIPLOME ON SPECIALITE.specialite_id = DIPLOME.specialite_id
			WHERE TRIM(SPECIALITE.specialite_opt1) IS NOT NULL AND TRIM(SPECIALITE.specialite_opt1) <> ''
			UNION ALL
			SELECT DISTINCT 
				TRIM(SPECIALITE.specialite_opt2) AS val,
				DIPLOME.diplome_serie_code AS code
			FROM SPECIALITE
			JOIN DIPLOME ON SPECIALITE.specialite_id = DIPLOME.specialite_id
			WHERE TRIM(SPECIALITE.specialite_opt2) IS NOT NULL AND TRIM(SPECIALITE.specialite_opt2) <> ''
			ORDER BY val, code
		";
	}

	public function hydrateSelectFilters(array &$config): void
	{
		$optionQueries = $this->getOptionQueries();

		foreach ($config['sections'] as &$section)
		{
			foreach ($section['filters'] as &$filter)
			{
				if (($filter['type'] ?? '') !== 'select' && ($filter['type'] ?? '') !== 'multiselect') { continue; }
				$name = $filter['name'] ?? null;
				if (!$name || !isset($optionQueries[$name])) { continue; }
				$query      = $optionQueries[$name];
				$statement  = $this->pdo->query($query['sql']);
				$options    = [];
				while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
				{
					$label = $query['label']($row);
					if ($label === null || $label === '') { continue; }
					$value = $row['val'] ?? $label;
					
					// Si la requête retourne un code (comme pour specialite_spe / specialite_opt), créer un objet
					if (isset($row['code']) && ($name === 'specialite_spe' || $name === 'specialite_opt'))
					{
						// Utiliser une clé unique valeur-code pour éviter les doublons
						$uniqueKey = $value . '|' . $row['code'];
						$options[$uniqueKey] = [
							'value' => $value,
							'label' => $label,
							'code'  => $row['code']
						];
					}
					else
					{
						$options[$value] = $label;
					}
				}
				if (!empty($options)) { $filter['options'] = $options; }
			}
		}
		unset($section, $filter);
	}

	public function linkGroupToCritere(int $groupeId, int $critereId): void
	{
		$sql  = "INSERT INTO FILTRE (groupe_id, critere_id) VALUES (:gid, :cid)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':gid', $groupeId, \PDO::PARAM_INT);
		$stmt->bindValue(':cid', $critereId, \PDO::PARAM_INT);
		$stmt->execute();
	}

	public function deleteByGroupeId(int $groupeId): void
	{
		$sql  = "DELETE FROM FILTRE WHERE groupe_id = :gid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':gid', $groupeId, \PDO::PARAM_INT);
		$stmt->execute();
	}
}
