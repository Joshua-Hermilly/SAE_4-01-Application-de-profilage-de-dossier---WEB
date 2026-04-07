<?php

require_once '../app/repositories/FormationStatsRepository.php';
require_once '../app/entities/FormationStat.php';

class FormationStatsService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private FormationStatsRepository $formationStatsRepository;

	/*-------------------------------*/
	/* Constructeur                  */
	/*-------------------------------*/
	public function __construct()
	{
		$this->formationStatsRepository = new FormationStatsRepository();
	}

	public function getAnneeReference(?int $annee = null): ?int
	{
		if ($annee !== null) { return $annee; }
		return $this->formationStatsRepository->getLatestYear();
	}

	public function findAtPage(int $page, ?int $annee = null, int $limit = 25): array
	{
		$anneeRef = $this->getAnneeReference($annee);
		$rows     = $this->formationStatsRepository->findByPage($page, $anneeRef, $limit);

		$result = [];
		foreach ($rows as $row)
		{
			$result[] = new FormationStat(
				$row['serie_bac']       ?? null,
				$row['combinaison_spe'] ?? null,
				(int)($row['total_voeux']    ?? 0),
				(int)($row['filles']         ?? 0),
				(int)($row['garcons']        ?? 0),
				(int)($row['boursiers']      ?? 0),
				(int)($row['non_boursiers']  ?? 0)
			);
		}

		return $result;
	}

	public function maxPage(?int $annee = null, int $limit = 25): int
	{
		$anneeRef = $this->getAnneeReference($annee);
		$total    = $this->formationStatsRepository->countRows($anneeRef);
		return max(1, (int) ceil($total / $limit));
	}
}
