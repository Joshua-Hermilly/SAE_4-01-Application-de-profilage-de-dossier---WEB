<?php

require_once '../app/repositories/ExportRepository.php';
require_once '../app/entities/Export.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/

	private $exportRepository;
	/*-------------------------------*/
	/*          Consctructeur        */
	/*-------------------------------*/
	public function __construct()
	{
		$this->exportRepository = new ExportRepository();
	}

	/*-------------------------------*/
	/*          Export               */
	/*-------------------------------*/
	public function exportXLSX(int $annee): void
	{
		$headers = $this->getHeader($annee);
		$donnees = $this->exportRepository->findAll($annee);

		$tableau = [];
		foreach ($donnees as $donnee)
		{
			$donnee->getDonnees();
		}

		$spreadsheet = new Spreadsheet();

		$feuille = $spreadsheet->getActiveSheet();
		$feuille->setTitle('Export');

		$feuille->fromArray($headers, null, 'A1');
		$feuille->fromArray($tableau, null, 'A2');

		$writer = new Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Total Promotion ' . ($annee - 1) . '-' . $annee . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit();
	}

	public function getHeader( $annee )
	{
		return
		[
			"Code Candidat",
			"Nom Candidat",
			"Prénom",
			"Civilité",
			"Profil Candidat - Libellé",
			"Candidat boursier - Code",
			"Filiere (pour scolarité du supérieur)- Libellé " . ($annee - 1) . "/" . ($annee),
			"Formation - Libellé (Saisie manuelle) "          . ($annee - 1) . "/" . ($annee),
			"Spécialité / Mention - Libellé "                 . ($annee - 1) . "/" . ($annee),
			"Nom Etablissement origine "                      . ($annee - 1) . "/" . ($annee),
			"Commune Etablissement origine - Libellé "        . ($annee - 1) . "/" . ($annee),
			"Commune Etablissement origine - CodePostal "     . ($annee - 1) . "/" . ($annee),
			"Département Etablissement origine - Libellé "    . ($annee - 1) . "/" . ($annee),
			"Pays Etablissement origine - Libellé "           . ($annee - 1) . "/" . ($annee),
			"Type Diplôme - Code",
			"Type Diplôme - Libellé",
			"Série Diplôme - Code",
			"Série Diplôme - Libellé",
			"Spécialité - Libellé",
			"Combinaison des enseignements de spécialité en Terminale",
			"Enseignement De spécialité abandonné en Première",
			"Note Globale Calculée",
			"Note Fiche Avenir",
			"Note Lycée calculée",
			"Note Dossier",
			"Commentaire",
		];
	}
}