<?php

require_once '../app/repositories/CritereRepository.php';
require_once '../app/repositories/GroupeRepository.php';
require_once '../app/repositories/FiltreRepository.php';

class GroupeFiltreService
{
	/*-------------------------------*/
	/*  Repositories                 */
	/*-------------------------------*/
	private CritereRepository $critereRepository;
	private GroupeRepository  $groupeRepository;
	private FiltreRepository  $filtreRepository;

	/*-------------------------------*/
	/*  Constructeur                 */
	/*-------------------------------*/
	public function __construct()
	{
		$this->critereRepository = new CritereRepository();
		$this->groupeRepository  = new GroupeRepository();
		$this->filtreRepository  = new FiltreRepository();
	}

	/*-------------------------------*/
	/*  Configuration des filtres    */
	/*-------------------------------*/
	public function buildFilterConfig(): array
	{
		$criteres   = $this->critereRepository->findAll();
		$noteBounds = $this->critereRepository->getGlobalMinMax();
		$nomsGroupe = $this->groupeRepository ->getDistinctNames();

		$critereOptions = [];
		foreach ($criteres as $critere)
		{
			$label = $critere->getCritereLibelle();
			$value = $critere->getCritereFiltre();
			if ($label === null || $label === '' || $value === null || $value === '') { continue; }
			$critereOptions[$value] = $label;
		}

		$minNote = $noteBounds['min'] ?? 0;
		$maxNote = $noteBounds['max'] ?? 20;

		$nomOptions = [];
		foreach ($nomsGroupe as $nom)
		{
			$trimmed = trim((string) $nom);
			if ($trimmed === '') { continue; }
			$nomOptions[$trimmed] = $trimmed;
		}

		$config = [
			'title'    => 'Filtres des groupes',
			'sections' => [
				'groupe' => [
					'label'   => 'Groupe',
					'filters' => [
						[
							'name'        => 'nom_groupe',
							'label'       => 'Nom du groupe',
							'column'      => 'GROUPE.groupe_nom',
							'type'        => 'select',
							'options'     => $nomOptions,
						],
					],
				],
				'criteres' => [
					'label'   => 'Critères',
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
						[
							'name'     => 'type_bac',
							'label'    => 'Type de Bac',
							'column'   => 'DIPLOME.diplome_type_libelle',
							'type'     => 'select',
							'options'  => [],
						],
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
				'notes' => [
					'label'   => 'Notes de dossier',
					'filters' => [
						[
							'name'     => 'note_dossier',
							'label'    => 'Note dossier',
							'column'   => 'GROUPE.groupe_note_dossier',
							'type'     => 'number',
							'operator' => '>=',
							'min'      => $minNote,
							'max'      => $maxNote,
						],
					],
				],
			],
		];

		$this->filtreRepository->hydrateSelectFilters($config);
		return $config;
	}
}
