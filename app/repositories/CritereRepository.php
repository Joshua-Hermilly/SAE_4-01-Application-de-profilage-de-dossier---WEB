<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Critere.php';

class CritereRepository
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*  Create                       */
	/*-------------------------------*/
	public function create(Critere $critere)
	{

	}

	/*-------------------------------*/
	/*  Insert                       */
	/*-------------------------------*/
}