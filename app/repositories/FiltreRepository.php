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
			'departement'     => [
				'sql'   => $this->selectDistinct('LOCALISATION', 'localisation_departement'),
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
		];
	}

	private function selectDistinct(string $table, string $column): string
	{
		return "SELECT DISTINCT CAST($column AS TEXT) AS val FROM $table WHERE $column IS NOT NULL ORDER BY CAST($column AS TEXT)";
	}

	private function unionDistinct(string $table, array $columns): string
	{
		$selects = [];
		foreach ($columns as $column)
		{
			$selects[] = "SELECT CAST($column AS TEXT) AS val FROM $table WHERE $column IS NOT NULL";
		}
		$union = implode(' UNION ALL ', $selects);
		return "SELECT DISTINCT TRIM(val) AS val FROM ($union) AS all_specs WHERE TRIM(val) <> '' ORDER BY val";
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

	/**
	 * Hydrate les filtres select/multiselect avec leurs options
	 */
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
					
					// Si la requête retourne un code (comme pour specialite_spe), créer un objet
					if (isset($row['code']) && $name === 'specialite_spe')
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
}
