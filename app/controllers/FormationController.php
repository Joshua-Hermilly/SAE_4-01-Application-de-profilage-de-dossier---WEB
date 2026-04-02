<?php

require_once '../app/core/Controller.php';

class FormationController extends Controller
{
	public function formation(): void
	{
		if (session_status() === PHP_SESSION_NONE)  session_start();

		if (empty($_SESSION['compte']))
		{
			$this->redirectTo('login.php');
			return;
		}

		$this->view('pages/formations', 'Formations');
	}
}