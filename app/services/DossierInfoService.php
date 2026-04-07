<?php

require_once '../app/repositories/DossierCandidatRepository.php';

class DossierInfoService
{
	public function getInfos($filterConfig): array
	{

		$dossierCandidatRepo = new DossierCandidatRepository();
		$nombreCandidats = $dossierCandidatRepo->nbMaxDossier($filterConfig);
		$anneesPossibles = $dossierCandidatRepo->findAnnees($filterConfig);

		return $config = [
			'nbEtu'    => $nombreCandidats,
			'annees'   => $anneesPossibles
		];
	}
}