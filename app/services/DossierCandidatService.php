<?php

require_once '../app/entities/DossierCandidat.php';

require_once '../app/repositories/DossierCandidatRepository.php';

class DossierCandidatService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $dossierCandidatRepository;

	/*-------------------------------*/
	/* Constructeur                  */
	/*-------------------------------*/
	function __construct()
	{
		$this->dossierCandidatRepository = new DossierCandidatRepository();
	}

	/*-------------------------------*/
	/* Requètes                      */
	/*-------------------------------*/
	public function findAllDossierCandidat(): array
	{
		return $this->dossierCandidatRepository->findAll();
	}

	public function findAtPageDossierCandidat(int $page, array $filters = []): array
	{
		return $this->dossierCandidatRepository->findByPage($page, $filters);
	}

	public function maxPage(array $filters = []): int
	{
		return max(1, (int) ceil($this->dossierCandidatRepository->nbMaxDossier($filters) / 25));
	}

}