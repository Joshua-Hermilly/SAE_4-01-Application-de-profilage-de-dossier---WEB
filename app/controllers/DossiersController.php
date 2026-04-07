<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierFiltreService.php';
require_once '../app/services/DossierInfoService.php';
require_once '../app/services/GroupeService.php';
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

		$editGroupId    = isset($_GET['groupe_id']) ? (int) $_GET['groupe_id'] : null;
		$editGroupName  = $_GET['groupe'] ?? $_GET['nom_groupe'] ?? null;
		$editGroupColor = $_GET['couleur'] ?? null;
		$editGroupNote  = isset($_GET['note']) && $_GET['note'] !== '' ? (float) $_GET['note'] : null;
		$modeParam      = $_GET['mode'] ?? null;
		$editGroupMode  = $modeParam === 'edit_groupe' && $editGroupId !== null;
		$viewGroupMode  = $modeParam === 'view_groupe' && $editGroupId !== null;

		$defaultFilters = [];
		$groupe        = null;
		if (($editGroupMode || $viewGroupMode) && $editGroupId !== null && $editGroupId > 0)
		{
			$groupeService = new GroupeService();
			$groupe        = $groupeService->findById($editGroupId);
			if ($groupe)
			{
				$defaultFilters = $groupeService->buildFilterValuesFromGroupe($groupe);

				if ($viewGroupMode)
				{
					$groupName = $groupe->getGroupeNom();
					if ($groupName !== null && $groupName !== '') { $defaultFilters['nom_groupe'] = $groupName; }
				}
			}
		}

		if ($editGroupMode && $groupe)
		{
			$editGroupName  = $groupe->getGroupeNom();
			$editGroupColor = $groupe->getGroupeCouleur();
			$editGroupNote  = $groupe->getGroupeNoteDossier();
		}

		$this->view('pages/dossiers', 'Dossiers', [
			'filterConfig'   => $filterConfig,
			'infoConfig'     => $infoConfig,
			'isAdmin'        => $_SESSION['compte']->getCompteIsAdmin(),
			'editGroupMode'  => $editGroupMode,
			'editGroupId'    => $editGroupId,
			'editGroupName'  => $editGroupName,
			'editGroupColor' => $editGroupColor,
			'editGroupNote'  => $editGroupNote,
			'defaultFilters' => $defaultFilters,
			'viewGroupMode'  => $viewGroupMode,
		]);
	}

}