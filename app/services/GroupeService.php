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

			if (!empty($codes))
			{
				$candidatRepo->assignGroupToCodes($groupe->getGroupeId(), $codes);
			}

			$pdo->commit();
			return $this->GroupeRepository->findById($groupe->getGroupeId()) ?? $groupe;
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
			foreach ($groupesId as $GroupeId)
			{
				$this->GroupeRepository->supprimer($GroupeId);
			}
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

}