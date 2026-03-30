<?php
// app/controllers/IndexController.php

require_once '../app/core/Controller.php';
require_once '../app/services/ImportService.php';


use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
	public function import()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			// Fichier présent
			//if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK)
			//{
			//	throw new \RuntimeException('Erreur lors de l\'upload');
			//}

			$tmpPath   = $_FILES['file']['tmp_name'];
			$origName  = $_FILES['file']['name'];

			// Valider l'extension
			$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
			if (!in_array($ext, ['xlsx', 'xls', 'csv']))
			{
				$this->view('import', 'titre', ['error' => 'Format de fichier non supporté']);
				return;
			}

			$spreadsheet = IOFactory::load($tmpPath);
			(new ImportService())->importFile($spreadsheet);

			$this->redirectTo('index');
		}

		$this->view('import');
	}
}
