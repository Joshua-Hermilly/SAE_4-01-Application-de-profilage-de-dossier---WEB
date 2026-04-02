<?php

require_once '../app/core/Controller.php';

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

		$this->view('pages/groupes', 'Groupes');
	}
}