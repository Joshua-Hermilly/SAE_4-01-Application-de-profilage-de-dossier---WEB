<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';
require_once '../app/core/Repository.php';

class DossiersController extends Controller
{
	/**
	 * Page Dossiers — Liste des candidats avec filtres & table dynamique
	 */
	public function dossiers(): void
	{
		$filterConfigAll = require '../config/filterConfig.php';
		$config = $filterConfigAll['dossiers'] ?? ['sections' => []];

		$pdo = Repository::getInstance()->getPDO();

		// Requêtes pour alimenter dynamiquement les filtres select
		$optionQueries = [
			'civilite' => [
				'sql' => "SELECT DISTINCT candidat_civilite AS val FROM CANDIDAT WHERE candidat_civilite IS NOT NULL AND candidat_civilite <> '' ORDER BY candidat_civilite",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'boursier' => [
				'sql' => "SELECT DISTINCT candidat_boursier_code AS val FROM CANDIDAT WHERE candidat_boursier_code IS NOT NULL ORDER BY candidat_boursier_code",
				'label' => static function (array $row): ?string {
					if (!isset($row['val'])) return null;
					return match ((string) $row['val']) {
						'0' => 'Non boursier',
						'1' => 'Boursier échelon 1',
						'2' => 'Boursier échelon 2',
						default => 'Boursier (' . $row['val'] . ')',
					};
				},
			],
			'type_bac' => [
				'sql' => "SELECT DISTINCT diplome_type_libelle AS val FROM DIPLOME WHERE diplome_type_libelle IS NOT NULL AND diplome_type_libelle <> '' ORDER BY diplome_type_libelle",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'serie_bac' => [
				'sql' => "SELECT DISTINCT diplome_serie_libelle AS val FROM DIPLOME WHERE diplome_serie_libelle IS NOT NULL AND diplome_serie_libelle <> '' ORDER BY diplome_serie_libelle",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_spe1' => [
				'sql' => "SELECT DISTINCT specialite_spe1 AS val FROM SPECIALITE WHERE specialite_spe1 IS NOT NULL AND specialite_spe1 <> '' ORDER BY specialite_spe1",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_spe2' => [
				'sql' => "SELECT DISTINCT specialite_spe2 AS val FROM SPECIALITE WHERE specialite_spe2 IS NOT NULL AND specialite_spe2 <> '' ORDER BY specialite_spe2",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'departement' => [
				'sql' => "SELECT DISTINCT localisation_departement AS val FROM LOCALISATION WHERE localisation_departement IS NOT NULL AND localisation_departement <> '' ORDER BY localisation_departement",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
		];

		// Remplir dynamiquement les options des filtres select à partir de la BD
		foreach ($config['sections'] as &$section) {
			foreach ($section['filters'] as &$filter) {
				if (($filter['type'] ?? '') !== 'select') {
					continue;
				}
				$name = $filter['name'] ?? null;
				if (!$name || !isset($optionQueries[$name])) {
					continue;
				}
				$query = $optionQueries[$name];
				$statement = $pdo->query($query['sql']);
				$options = [];
				while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
					$label = $query['label']($row);
					if ($label === null || $label === '') {
						continue;
					}
					$options[$label] = $label;
				}
				if (!empty($options)) {
					$filter['options'] = $options;
				}
			}
		}
		unset($section, $filter);

		$this->view('pages/dossiers', 'Dossiers', [
			'filterConfig' => $config,
			'pages' => 'dossiers'
		]);
	}
}
