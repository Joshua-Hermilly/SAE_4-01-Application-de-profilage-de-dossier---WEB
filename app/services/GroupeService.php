<?php

require_once '../app/entities/Groupe.php';

require_once '../app/repositories/GroupeRepository.php';

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

}