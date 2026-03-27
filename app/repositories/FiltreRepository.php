<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Filtre.php';
require_once '../app/repositories/GroupeRepository.php';
require_once '../app/repositories/CritereRepository.php';


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
	/*  Méthodes                     */
	/*-------------------------------*/
	public function create(Filtre $filtre): void
	{
		$groupe_id = $filtre->getGroupe()->getGroupeId();
		foreach ($filtre->getCriteres() as $critere)
		{
			$sql  = "INSERT INTO filtre (groupe_id, critere_id) VALUES (:groupe_id, :critere_id)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':groupe_id' , $groupe_id, PDO::PARAM_INT);
			$stmt->bindValue(':critere_id', $critere->getCritereId(), PDO::PARAM_INT);
			$stmt->execute();
		}
	}

	public function getFiltreByGroupeId(int $groupe_id): ?Filtre
	{
		$groupe = (new GroupeRepository())->getGroupeById($groupe_id);
		if (!$groupe) { return null; }

		$criteres = (new CritereRepository())->findByGroupe($groupe_id);

		return new Filtre($groupe, $criteres);
	}
}