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

	public function findAtPageDossierCandidat(int $page): array
	{
		return $this->GroupeRepository->findByPage($page);
	}

	public function maxPage(): int
	{
		return (int) ($this->GroupeRepository->nbMaxDossier() / 25) +1;
	}

}