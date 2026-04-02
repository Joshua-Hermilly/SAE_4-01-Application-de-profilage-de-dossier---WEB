<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierFiltreService.php';

class DossiersController extends Controller
{
	public function dossiers(): void
	{
		if (session_status() === PHP_SESSION_NONE) { session_start(); }

		$filtreService = new DossierFiltreService();
		$dataVersion   = $_SESSION['data_version'] ?? '0';
		$filtreCache   = $_SESSION['filter_cache'] ?? null;

		if (is_array($filtreCache) && ($filtreCache['version'] ?? '') === $dataVersion) { $filterConfig = $filtreCache['data']; }
		else
		{
			$filterConfig = $filtreService->buildFilterConfig();
			$_SESSION['filter_cache'] = [
				'version' => $dataVersion,
				'data'    => $filterConfig,
			];
		}

		$this->view('pages/dossiers', 'Dossiers', ['filterConfig' => $filterConfig]);
	}

}