<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierFiltreService.php';
require_once '../app/entities/Compte.php';

class DossiersController extends Controller
{
	public function dossiers(): void
	{
		if (session_status() === PHP_SESSION_NONE) { session_start(); }

		$filtreService = new DossierFiltreService();
		$filterConfig  = $filtreService->buildFilterConfig();

		$this->view('pages/dossiers', 'Dossiers', ['filterConfig' => $filterConfig , 'isAdmin' => $_SESSION['compte']->getCompteIsAdmin() ]);
	}

}