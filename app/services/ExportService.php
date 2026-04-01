<?php

require_once '../app/core/ExportRepository.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
	private $exportRepository;

	public function __construct()
	{
		$this->exportRepository = new ExportRepository();
	}

	public function exportXLSX(int $annee): void
	{
		$headers = $this->exportRepository->getHeader ($annee);
		$donnees = $this->exportRepository->getDonnees($annee);

		$spreadsheet = new Spreadsheet();

		$feuille = $spreadsheet->getActiveSheet();
		$feuille->setTitle('Export');

		$feuille->fromArray($headers, null, 'A1');
		$feuille->fromArray($donnees, null, 'A2');

		$writer = new Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Total Promotion ' . ($annee - 1) . '-' . $annee . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit();
	}
}