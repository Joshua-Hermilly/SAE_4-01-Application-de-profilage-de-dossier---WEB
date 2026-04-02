<?php

require_once '../app/repositories/CritereRepository.php';
require_once '../app/repositories/GroupeRepository.php';

class GroupeFiltreService
{
	/*-------------------------------*/
	/*  Repositories                 */
	/*-------------------------------*/
	private CritereRepository $critereRepository;
	private GroupeRepository  $groupeRepository;

	/*-------------------------------*/
	/*  Constructeur                 */
	/*-------------------------------*/
	public function __construct()
	{
		$this->critereRepository = new CritereRepository();
		$this->groupeRepository  = new GroupeRepository();
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

		return [
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
					'label'   => 'Critères associés',
					'filters' => [
						[
							'name'    => 'critere',
							'label'   => 'Critères',
							'column'  => 'CRITERE.critere_filtre',
							'type'    => 'multiselect',
							'options' => $critereOptions,
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
	}
}
