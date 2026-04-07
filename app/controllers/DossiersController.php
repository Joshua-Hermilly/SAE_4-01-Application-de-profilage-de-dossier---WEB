<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierFiltreService.php';
require_once '../app/services/DossierInfoService.php';
require_once '../app/entities/Compte.php';

class DossiersController extends Controller
{
	public function dossiers(): void
	{
		if (session_status() === PHP_SESSION_NONE) { session_start(); }

		$filtreService = new DossierFiltreService();
		$filterConfig  = $filtreService->buildFilterConfig();

		$infoService   = new DossierInfoService($filterConfig);
		$infoConfig    = $infoService->getInfos($filterConfig);

		$this->view('pages/dossiers', 'Dossiers', ['filterConfig' => $filterConfig , 'infoConfig' => $infoConfig, 'isAdmin' => $_SESSION['compte']->getCompteIsAdmin() ]);
	}

}