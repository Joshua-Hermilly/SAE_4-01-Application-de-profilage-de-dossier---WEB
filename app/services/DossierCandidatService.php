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

	public function findAtPageDossierCandidat(int $page): array
	{
		return $this->dossierCandidatRepository->findByPage($page);
	}

	public function maxPage(): int
	{
		return (int) ($this->dossierCandidatRepository->nbMaxDossier() / 25) +1;
	}

}