<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Groupe.php';
require_once '../app/entities/Critere.php';

require_once '../app/repositories/GroupeRepository.php';
require_once '../app/repositories/CritereRepository.php';
require_once '../app/repositories/FiltreRepository.php';
require_once '../app/repositories/CandidatRepository.php';

class GroupeService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $GroupeRepository;

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
			if ($noteDossier !== null)
			{
				$noteDossier = max(0.0, min(20.0, $noteDossier));
			}

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
			if (!$groupe)
			{
				throw new RuntimeException('Groupe introuvable');
			}

			$finalNom = ($nom !== '') ? $nom : $groupe->getGroupeNom();
			if ($noteDossier !== null)
			{
				$noteDossier = max(0.0, min(20.0, $noteDossier));
			}
			$groupe->setGroupeNom($finalNom);
			$groupe->setGroupeCouleur($couleur);
			$groupe->setGroupeNoteDossier($noteDossier);
			$this->GroupeRepository->update($groupe);

			$critereRepo  = new CritereRepository();
			$filtreRepo   = new FiltreRepository();
			$candidatRepo = new CandidatRepository();

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
		$criteres = [];

		$discreteKeys = ['civilite', 'boursier', 'type_bac', 'serie_bac', 'specialite_spe', 'specialite_opt'];
		foreach ($discreteKeys as $key)
		{
			if (!isset($filters[$key]) || $filters[$key] === '' || $filters[$key] === null) { continue; }
			$values = is_array($filters[$key]) ? $filters[$key] : [$filters[$key]];
			foreach ($values as $value)
			{
				if ($value === '' || $value === null) { continue; }
				$criteres[] = new Critere(0, $key, (string) $value, 0.0, 0.0);
			}
		}

		$noteKeys = ['note_lycee', 'note_fiche', 'note_globale'];
		foreach ($noteKeys as $base)
		{
			$minKey  = $base . '_min';
			$maxKey  = $base . '_max';
			$hasMin  = isset($filters[$minKey]) && $filters[$minKey] !== '' && $filters[$minKey] !== null;
			$hasMax  = isset($filters[$maxKey]) && $filters[$maxKey] !== '' && $filters[$maxKey] !== null;
			if (!$hasMin && !$hasMax) { continue; }

			$min = $hasMin ? (float) $filters[$minKey] : 0.0;
			$max = $hasMax ? (float) $filters[$maxKey] : 20.0;
			$criteres[] = new Critere(0, $base, $base, $min, $max);
		}

		return $criteres;
	}

	public function buildFilterValuesFromGroupe(Groupe $groupe): array
	{
		$filters = [];

		$discreteKeysSingle = ['civilite', 'boursier', 'type_bac', 'serie_bac'];
		$discreteKeysMulti  = ['specialite_spe', 'specialite_opt'];
		$noteKeys           = ['note_lycee', 'note_fiche', 'note_globale'];

		foreach ($groupe->getCriteres() as $critere)
		{
			if (!$critere instanceof Critere) { continue; }

			$label = $critere->getCritereLibelle();
			$value = $critere->getCritereFiltre();
			$min   = $critere->getCritereMin();
			$max   = $critere->getCritereMax();

			if (in_array($label, $discreteKeysSingle, true)) { $filters[$label] = $value; continue; }

			if (in_array($label, $discreteKeysMulti, true))
			{
				if (!isset($filters[$label]) || !is_array($filters[$label])) { $filters[$label] = []; }
				$filters[$label][] = $value;
				continue;
			}

			if (in_array($label, $noteKeys, true))
			{
				$minKey = $label . '_min';
				$maxKey = $label . '_max';
				$filters[$minKey] = $min;
				$filters[$maxKey] = $max;
				continue;
			}
		}

		return $filters;
	}

}