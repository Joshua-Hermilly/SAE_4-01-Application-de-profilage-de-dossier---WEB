<?php

require_once '../app/core/Controller.php';
require_once '../app/services/ExportService.php';

class ExportController extends Controller
{
	public function export(): void
	{
		$errors = [];

		if (session_status() === PHP_SESSION_NONE)  session_start();

		if (empty($_SESSION['compte']))
		{
			$this->redirectTo('login.php');
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$annee  = $_POST['annee_promotion'] ?? $_GET['annee'] ?? null;
			if ($annee === null || !preg_match('/^\d{4}$/', (string)$annee))
			{
				$errors[] = "Paramètre 'annee' manquant ou invalide (format AAAA).";
				$this->view('export', 'Export', ['errors' => $errors]);
				return;
			}

			try                  { (new ExportService())->exportXLSX((int)$annee); }
			catch (Throwable $e)
			{
				error_log("Erreur lors de l'export : " . $e->getMessage());
				$errors[] = "Une erreur est survenue lors de l'export. Veuillez réessayer plus tard.";
				$this->view('export', 'Export', ['errors' => $errors]);
				return;
			}
		}

		$this->view('export', 'Exportation', ['errors' => $errors]);
	}
}
