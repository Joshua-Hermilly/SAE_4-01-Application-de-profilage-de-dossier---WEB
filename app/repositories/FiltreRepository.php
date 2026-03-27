<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/entities/Filtre.php';


class FiltreRepository
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*  Requetes                     */
	/*-------------------------------*/
	private function createLocalisationFromRow(array $row): ?Filtre
	{
		return null;
	}
}