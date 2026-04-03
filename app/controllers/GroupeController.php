<?php

require_once '../app/core/Controller.php';
require_once '../app/services/GroupeFiltreService.php';

class GroupeController extends Controller
{
	public function groupe(): void
	{
		if (session_status() === PHP_SESSION_NONE)  session_start();

		if (empty($_SESSION['compte']))
		{
			$this->redirectTo('login.php');
			return;
		}

		$filtreService = new GroupeFiltreService();
		$filterConfig  = $filtreService->buildFilterConfig();

		$this->view('pages/groupes', 'Groupes', ['filterConfig' => $filterConfig]);
	}
}