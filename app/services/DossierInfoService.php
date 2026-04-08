<?php

require_once '../app/repositories/DossierCandidatRepository.php';

class DossierInfoService
{
	public function getInfos(array $filters = []): array
	{

		$dossierCandidatRepo = new DossierCandidatRepository();
		$nombreCandidats = $dossierCandidatRepo->nbMaxDossier($filters);
		$anneesPossibles = $dossierCandidatRepo->findAnnees  ($filters);

		return $config = [
			'nbEtu'    => $nombreCandidats,
			'annees'   => $anneesPossibles
		];
	}
}