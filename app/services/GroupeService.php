<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Groupe.php';
require_once '../app/entities/Critere.php';

require_once '../app/repositories/GroupeRepository.php';
require_once '../app/repositories/CritereRepository.php';
require_once '../app/repositories/FiltreRepository.php';
require_once '../app/repositories/CandidatRepository.php';
require_once '../app/services/DossierFiltreService.php';

class GroupeService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $GroupeRepository;
	private ?array $definitionsFiltres = null;

	/*-------------------------------*/
	/* Constructeur                  */
	/*-------------------------------*/
	function __construct()
	{
		$this->GroupeRepository = new GroupeRepository();
	}

	public function findById(int $id): ?Groupe
	{
		return $this->GroupeRepository->findById($id);
	}

	/*-------------------------------*/
	/* Requètes                      */
	/*-------------------------------*/
	public function findAllDossierCandidat(): array
	{
		return $this->GroupeRepository->findAll();
	}

	public function findAtPageDossierCandidat(int $page, array $filters = []): array
	{
		return $this->GroupeRepository->findByPage($page, $filters);
	}

	public function maxPage(array $filters = []): int
	{
		return max(1, (int) ceil($this->GroupeRepository->nbMaxDossier($filters) / 25));
	}

	public function creerGroupeDepuisSelection(string $nom, string $couleur, ?float $noteDossier, array $codes, array $filters = []): Groupe
	{
		$pdo = Repository::getInstance()->getPDO();
		$pdo->beginTransaction();

		try
		{
			// Forcer la note éventuelle dans l'intervalle [0,20]
			if ($noteDossier !== null) { $noteDossier = max(0.0, min(20.0, $noteDossier)); }

			$groupe = new Groupe(0, $nom, $couleur, $noteDossier, [], []);
			$this->GroupeRepository->create($groupe);

			$critereRepo = new CritereRepository();
			$filtreRepo  = new FiltreRepository();
			$candidatRepo= new CandidatRepository();

			$criteres = $this->buildCriteresFromFilters($filters);
			foreach ($criteres as $critere)
			{
				$critereRepo->create($critere);
				$filtreRepo->linkGroupToCritere($groupe->getGroupeId(), $critere->getCritereId());
			}

			if (!empty($codes)) { $candidatRepo->assignGroupToCodes($groupe->getGroupeId(), $codes); }

			$pdo->commit();
			return $this->GroupeRepository->findById($groupe->getGroupeId()) ?? $groupe;
		}
		catch (\Throwable $e)
		{
			if ($pdo->inTransaction()) { $pdo->rollBack(); }
			throw $e;
		}
	}

	public function mettreAJourGroupe(int $groupeId, string $nom, string $couleur, ?float $noteDossier, array $codes, array $filters = []): Groupe
	{
		$pdo = Repository::getInstance()->getPDO();
		$pdo->beginTransaction();

		try
		{
			$groupe = $this->GroupeRepository->findById($groupeId);
			if (!$groupe) { throw new RuntimeException('Groupe introuvable'); }

			$finalNom = ($nom !== '') ? $nom : $groupe->getGroupeNom();
			// Forcer la note éventuelle dans l'intervalle [0,20]
			if ($noteDossier !== null) { $noteDossier = max(0.0, min(20.0, $noteDossier)); }
			$groupe->setGroupeNom        ($finalNom   );
			$groupe->setGroupeCouleur    ($couleur    );
			$groupe->setGroupeNoteDossier($noteDossier);
			$this->GroupeRepository->update($groupe);

			$critereRepo  = new CritereRepository();
			$filtreRepo   = new FiltreRepository();
			$candidatRepo = new CandidatRepository();

			// Réinitialiser critères
			$filtreRepo->deleteByGroupeId($groupeId);
			$criteres = $this->buildCriteresFromFilters($filters);
			foreach ($criteres as $critere)
			{
				$critereRepo->create($critere);
				$filtreRepo->linkGroupToCritere($groupeId, $critere->getCritereId());
			}

			$candidatRepo->removeGroupAssignmentsExcept($groupeId, $codes);
			if (!empty($codes)) { $candidatRepo->assignGroupToCodes($groupeId, $codes); }

			$pdo->commit();
			return $this->GroupeRepository->findById($groupeId) ?? $groupe;
		}
		catch (\Throwable $e)
		{
			if ($pdo->inTransaction()) { $pdo->rollBack(); }
			throw $e;
		}
	}

	public function supprimerGroupes($groupesId): bool
	{
		$pdo = Repository::getInstance()->getPDO();
		$pdo->beginTransaction();

		try
		{
			foreach ($groupesId as $GroupeId) { $this->GroupeRepository->supprimer($GroupeId); }
			$pdo->commit();
			return true;
		}
		catch (\Throwable $e)
		{
			if ($pdo->inTransaction()) { $pdo->rollBack(); }
			throw $e;
		}
	}

	private function buildCriteresFromFilters(array $filters): array
	{
		$criteres    = [];
		$definitions = $this->getDefinitionsFiltres();

		foreach ($definitions as $nom => $meta)
		{
			// On ne stocke pas le nom du groupe lui-même comme critère
			if ($nom === 'nom_groupe') { continue; }

			$estNote     = ($meta['estNote'    ] ?? false);
			$estMultiple = ($meta['estMultiple'] ?? false);

			if ($estNote)
			{
				$cleMin = $nom . '_min';
				$cleMax = $nom . '_max';
				$aMin   = isset($filters[$cleMin]) && $filters[$cleMin] !== '' && $filters[$cleMin] !== null;
				$aMax   = isset($filters[$cleMax]) && $filters[$cleMax] !== '' && $filters[$cleMax] !== null;
				if (!$aMin && !$aMax) { continue; }

				$min = $aMin ? (float) $filters[$cleMin] : 0.0;
				$max = $aMax ? (float) $filters[$cleMax] : 20.0;
				$criteres[] = new Critere(0, $nom, $nom, $min, $max);
				continue;
			}

			if (!isset($filters[$nom]) || $filters[$nom] === '' || $filters[$nom] === null) { continue; }
			$valeurs = is_array($filters[$nom]) ? $filters[$nom] : [$filters[$nom]];
			foreach ($valeurs as $valeur)
			{
				if ($valeur === '' || $valeur === null) { continue; }
				$criteres[] = new Critere(0, $nom, (string) $valeur, 0.0, 0.0);
			}
		}
		return $criteres;
	}

	public function buildFilterValuesFromGroupe(Groupe $groupe): array
	{
		$filters     = [];
		$definitions = $this->getDefinitionsFiltres();

		foreach ($groupe->getCriteres() as $critere)
		{
			if (!$critere instanceof Critere) { continue; }

			$label = $critere->getCritereLibelle();
			if (!isset($definitions[$label])) { continue; }

			$meta        = $definitions[$label];
			$estNote     = ($meta['estNote'    ] ?? false);
			$estMultiple = ($meta['estMultiple'] ?? false);

			$valeur = $critere->getCritereFiltre();
			$min    = $critere->getCritereMin();
			$max    = $critere->getCritereMax();

			if ($estNote)
			{
				$minKey = $label . '_min';
				$maxKey = $label . '_max';
				$filters[$minKey] = $min;
				$filters[$maxKey] = $max;
				continue;
			}

			if ($estMultiple)
			{
				if (!isset($filters[$label]) || !is_array($filters[$label])) { $filters[$label] = []; }
				$filters[$label][] = $valeur;
				continue;
			}
			$filters[$label] = $valeur;
		}
		return $filters;
	}

	private function getDefinitionsFiltres(): array
	{
		if ($this->definitionsFiltres !== null) { return $this->definitionsFiltres; }

		$service = new DossierFiltreService();
		$config  = $service->buildFilterConfig();
		$defs    = [];

		if (isset($config['sections']) && is_array($config['sections']))
		{
			foreach ($config['sections'] as $section)
			{
				if (!isset($section['filters']) || !is_array($section['filters'])) { continue; }
				foreach ($section['filters'] as $filter)
				{
					if (!isset($filter['name'])) { continue; }
					$nom  = $filter['name'];
					$type = $filter['type'] ?? 'select';
					$defs[$nom] = [
						'type'        => $type,
						'estNote'     => ($type === 'number'),
						'estMultiple' => ($type === 'multiselect'),
					];
				}
			}
		}

		$this->definitionsFiltres = $defs;
		return $defs;
	}

}