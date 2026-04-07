<?php
require_once '../app/entities/Compte.php';
require_once '../app/core/Controller.php';
require_once '../app/services/ImportService.php';


use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
	public function import()
	{
		$errors = [];

		if (session_status() === PHP_SESSION_NONE)  session_start();

		if (empty($_SESSION['compte']))
		{
			$this->redirectTo('login.php');
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			//année
			$annee     = $_POST['annee_promotion'];

			//fichier
			$tmpPath   = $_FILES['file']['tmp_name'];
			$origName  = $_FILES['file']['name'];

			// Valider l'extension
			if (!preg_match('/^\d{4}$/', $annee))
			{
				$errors[] = 'L\'année renseignée n\'est pas valide';
			}

			$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
			if (!in_array($ext, ['xlsx', 'xls', 'csv']))
			{
				$errors[] = 'Format de fichier non supporté';
			}

			if (empty($errors))
			{
				$spreadsheet = IOFactory::load($tmpPath);
				(new ImportService())->importFile($spreadsheet, $annee);

				$this->redirectTo('index');
				return;
			}
			else
			{
				$data['errors' ] = $errors;
			}
		}

		$this->view('import', 'Importation', [ 'errors' => $errors, 'isAdmin' => $_SESSION['compte']->getCompteIsAdmin() ]);
	}
}
