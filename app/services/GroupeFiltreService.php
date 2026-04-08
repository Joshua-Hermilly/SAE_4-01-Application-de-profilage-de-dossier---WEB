<?php

require_once '../app/repositories/CritereRepository.php';
require_once '../app/repositories/FiltreRepository.php';

class GroupeFiltreService
{
	/*-------------------------------*/
	/*  Repositories                 */
	/*-------------------------------*/
	private CritereRepository $critereRepository;
	private FiltreRepository  $filtreRepository;

	/*-------------------------------*/
	/*  Constructeur                 */
	/*-------------------------------*/
	public function __construct()
	{
		$this->critereRepository = new CritereRepository();
		$this->filtreRepository  = new FiltreRepository();
	}

	/*-------------------------------*/
	/*  Configuration des filtres    */
	/*-------------------------------*/
	public function buildFilterConfig(): array
	{
		$noteBounds = $this->critereRepository->getGlobalMinMax();
		$minNote   = $noteBounds['min'] ?? 0;
		$maxNote   = $noteBounds['max'] ?? 20;

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
							'options'     => [],
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
