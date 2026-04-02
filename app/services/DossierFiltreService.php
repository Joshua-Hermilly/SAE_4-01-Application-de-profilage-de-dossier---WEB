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
					'join'    => 'LEFT JOIN SPECIALITE ON SPECIALITE.diplome_id = CANDIDAT.diplome_id',
					'filters' => [
						[
							'name'     => 'specialite_spe',
							'label'    => 'Spécialités',
							'column'   => 'SPECIALITE.specialite_spe1',
							'type'     => 'multiselect',
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

		$this->filtreRepository->hydrateSelectFilters($config);
		return $config;
	}
}
