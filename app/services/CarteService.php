<?php

require_once '../app/entities/Localisation.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/repositories/EtablissementRepository.php';

class CarteService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $repository;

	/*-------------------------------*/
	/* Constructeur                  */
	/*-------------------------------*/
	function __construct()
	{
		$this->repository = new EtablissementRepository();
	}

	/*-------------------------------*/
	/* Requètes                      */
	/*-------------------------------*/
	public function findAll(): array
	{
		return $this->repository->findAll();
	}

	public function getByDistance($distance)
	{
		return $this->repository->findByDistance($distance);
	}

	public function getMaxDistance()
	{
		return $this->repository->getMaxDistance();
	}

}