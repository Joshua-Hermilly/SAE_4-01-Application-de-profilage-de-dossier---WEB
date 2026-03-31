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
		$filterConfig = $this->buildFilterConfig();

		$pdo           = Repository::getInstance()->getPDO();
		$optionQueries = $this->getOptionQueries();

		$this->hydrateSelectFilters($filterConfig, $optionQueries, $pdo);

		$this->view(
			'pages/dossiers',
			'Dossiers',
			[
				'filterConfig' => $filterConfig,
				'pages'        => 'dossiers',
			]
		);
	}

	private function buildFilterConfig(): array
	{
		return [
			'title'    => 'Filtres des dossiers',
			'sections' => [
				'candidat' => [
					'label'   => 'Candidat',
					'join'    => null,
					'filters' => [
						[
							'name'        => 'civilite',
							'label'       => 'Civilité',
							'column'      => 'CANDIDAT.candidat_civilite',
							'type'        => 'select',
							'options'     => [],
							'bac_tags'    => ['general', 'technologique', 'professionnel'],
						],
						[
							'name'        => 'boursier',
							'label'       => 'Statut Boursier',
							'column'      => 'CANDIDAT.candidat_boursier_code',
							'type'        => 'select',
							'options'     => [],
							'bac_tags'    => ['general', 'technologique', 'professionnel'],
						],
						[
							'name'        => 'profil',
							'label'       => 'Profil',
							'column'      => 'CANDIDAT.candidat_profil',
							'type'        => 'text',
							'placeholder' => 'Ex: En terminale',
							'bac_tags'    => ['general', 'technologique', 'professionnel'],
						],
					],
				],
				'diplome' => [
					'label'   => 'Diplôme / Bac',
					'join'    => 'LEFT JOIN DIPLOME ON DIPLOME.diplome_id = CANDIDAT.diplome_id',
					'filters' => [
						[
							'name'     => 'type_bac',
							'label'    => 'Type de Bac',
							'column'   => 'DIPLOME.diplome_type_libelle',
							'type'     => 'select',
							'options'  => [],
						],
					],
				],
				'serie_bac' => [
					'label'   => 'Série de Bac (Techno/Pro)',
					'join'    => 'LEFT JOIN DIPLOME ON DIPLOME.diplome_id = CANDIDAT.diplome_id',
					'filters' => [
						[
							'name'     => 'serie_bac',
							'label'    => 'Série',
							'column'   => 'DIPLOME.diplome_serie_libelle',
							'type'     => 'select',
							'options'  => [],
							],
					],
				],
				'specialites' => [
					'label'   => 'Spécialités',
					'join'    => 'LEFT JOIN SPECIALITE ON SPECIALITE.diplome_id = DIPLOME.diplome_id',
					'filters' => [
						[
							'name'     => 'specialite_spe1',
							'label'    => 'Spécialité 1',
							'column'   => 'SPECIALITE.specialite_spe1',
							'type'     => 'select',
							'options'  => [],
						],
						[
							'name'     => 'specialite_spe2',
							'label'    => 'Spécialité 2',
							'column'   => 'SPECIALITE.specialite_spe2',
							'type'     => 'select',
							'options'  => [],
						],
					],
				],
				'notes' => [
					'label'   => 'Notes',
					'join'    => null,
					'filters' => [
						[
							'name'     => 'note_lycee',
							'label'    => 'Note Lycée',
							'column'   => 'CANDIDAT.candidat_note_lycee',
							'type'     => 'number',
							'operator' => '>=',
							'min'      => 0,
							'max'      => 20,
							],
							[
								'name'     => 'note_fiche',
							'label'    => 'Note Fiche Avenir',
							'column'   => 'CANDIDAT.candidat_note_fiche',
							'type'     => 'number',
							'operator' => '>=',
							'min'      => 0,
							'max'      => 20,
						],
						[
							'name'     => 'note_globale',
							'label'    => 'Note Globale',
							'column'   => 'CANDIDAT.candidat_note_globale',
							'type'     => 'number',
							'operator' => '>=',
							'min'      => 0,
							'max'      => 20,
						],
					],
				],
				'etablissement' => [
					'label'   => 'Établissement',
					'join'    => 'LEFT JOIN ETABLISSEMENT ON ETABLISSEMENT.etablissement_id = CANDIDAT.etablissement_id LEFT JOIN LOCALISATION ON LOCALISATION.localisation_id = ETABLISSEMENT.localisation_id',
					'filters' => [
						[
							'name'     => 'departement',
							'label'    => 'Département',
							'column'   => 'LOCALISATION.localisation_departement',
							'type'     => 'select',
							'options'  => [],
						],
					],
				],
			],
		];
	}

	private function getOptionQueries(): array
	{
		return [
			'civilite'        => [
				'sql'   => "SELECT DISTINCT candidat_civilite AS val FROM CANDIDAT WHERE candidat_civilite IS NOT NULL AND candidat_civilite <> '' ORDER BY candidat_civilite",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'boursier'        => [
				'sql'   => "SELECT DISTINCT candidat_boursier_code AS val FROM CANDIDAT WHERE candidat_boursier_code IS NOT NULL ORDER BY candidat_boursier_code",
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
				'sql'   => "SELECT DISTINCT diplome_type_libelle AS val FROM DIPLOME WHERE diplome_type_libelle IS NOT NULL AND diplome_type_libelle <> '' ORDER BY diplome_type_libelle",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'serie_bac'       => [
				'sql'   => "SELECT DISTINCT diplome_serie_code   AS val FROM DIPLOME WHERE diplome_serie_code IS NOT NULL AND diplome_serie_code    <> '' ORDER BY diplome_serie_code",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_spe1' => [
				'sql'   => "SELECT DISTINCT specialite_spe1 AS val FROM SPECIALITE WHERE specialite_spe1 IS NOT NULL AND specialite_spe1 <> '' ORDER BY specialite_spe1",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'specialite_spe2' => [
				'sql'   => "SELECT DISTINCT specialite_spe2 AS val FROM SPECIALITE WHERE specialite_spe2 IS NOT NULL AND specialite_spe2 <> '' ORDER BY specialite_spe2",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
			'departement'     => [
				'sql'   => "SELECT DISTINCT localisation_departement AS val FROM LOCALISATION WHERE localisation_departement IS NOT NULL AND localisation_departement <> '' ORDER BY localisation_departement",
				'label' => static fn(array $row): ?string => $row['val'] ?? null,
			],
		];
	}

	private function hydrateSelectFilters(array &$config, array $optionQueries, \PDO $pdo): void
	{
		foreach ($config['sections'] as &$section)
		{
			foreach ($section['filters'] as &$filter)
			{
				if (($filter['type'] ?? '') !== 'select') { continue; }
				$name = $filter['name'] ?? null;
				if (!$name || !isset($optionQueries[$name])) { continue; }
				$query      = $optionQueries[$name];
				$statement  = $pdo->query($query['sql']);
				$options    = [];
				while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
				{
					$label = $query['label']($row);
					if ($label === null || $label === '') { continue; }
					$value           = $row['val'] ?? $label;
					$options[$value] = $label;
				}
				if (!empty($options)) { $filter['options'] = $options; }
			}
		}
		unset($section, $filter);
	}
}