<?php
// app/controllers/IndexController.php

require_once '../app/core/Controller.php';
require_once '../app/entities/Compte.php';

class IndexController extends Controller
{
	public function index(): void
	{
		if (session_status() === PHP_SESSION_NONE)  session_start();

		if (empty($_SESSION['compte']))
		{
			$this->redirectTo('login.php');
			return;
		}

		$this->view('pages/dossiers', 'Dossiers');
	}
}
