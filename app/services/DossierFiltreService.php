<?php

require_once '../app/repositories/FiltreRepository.php';

class DossierFiltreService
{
	/*-------------------------------*/
	/*  Repository                   */
	/*-------------------------------*/
	private FiltreRepository $filtreRepository;

	/*-------------------------------*/
	/*  Constructeur                 */
	/*-------------------------------*/
	public function __construct()
	{
		$this->filtreRepository = new FiltreRepository();
	}

	/*-------------------------------*/
	/*  Configuration des filtres    */
	/*-------------------------------*/
	public function buildFilterConfig(): array
	{
		$config = [
			'title'    => 'Filtres des dossiers',
			'sections' => [
				'anneee' => [
					'label'   => 'Année',
					'filters' => [
						[
							'name'     => 'annee',
							'label'    => 'Année de candidature',
							'column'   => 'CANDIDAT.candidat_annee',
							'type'     => 'select',
							'options'  => [],
						],
					],
				],
				'candidat' => [
					'label'   => 'Candidat',
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
					'label'   => 'Série de Bac',
					'filters' => [
						[
							'name'     => 'serie_bac',
							'label'    => 'Série',
							'column'   => 'DIPLOME.diplome_serie_libelle',
							'type'     => 'select',
							'options'  => [],
						],
						[
							'name'     => 'specialite_spe',
							'label'    => 'Spécialités',
							'column'   => 'SPECIALITE.specialite_spe1',
							'type'     => 'multiselect',
							'options'  => [],
						],
						[
							'name'     => 'specialite_opt',
							'label'    => 'Options',
							'column'   => 'SPECIALITE.specialite_opt1',
							'type'     => 'multiselect',
							'options'  => [],
						],
					],
				],
				'notes' => [
					'label'   => 'Notes',
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
			],
		];

		$this->filtreRepository->hydrateSelectFilters($config);
		return $config;
	}
}
