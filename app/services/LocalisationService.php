<?php

class LocalisationService
{
	/*-------------------------------*/
	/* COLONNES                      */
	/*-------------------------------*/
	public const COLONNE =
	[
		'ID'           ,
		'Etablissement',
		'Pays'         ,
		'Departement'  ,
		'Code Postale' ,
		'Commune'
	];

	/*-------------------------------*/
	/* REPOSITORY                    */
	/*-------------------------------*/
	private $repoEtablissement;
	private $repoLocalisation;

	/*-------------------------------*/
	/* ENTITY                        */
	/*-------------------------------*/
	private array $etablissements;
	private array $localisations;

	/*-------------------------------*/
	/* CONSTRUCT                     */
	/*-------------------------------*/
	function __construct()
	{
		$this->repoEtablissement = new EtablissementRepository();
		$this->repoLocalisation  = new LocalisationRepository();
	}

	private function CreerLigneEtablissement(): array
	{
		$rows = [];
		$etablissements = $this->repoEtablissement->findAll();

		foreach ($etablissements as $etablissement)
		{
			$localisation = $etablissement->getLocalisation();
			$rows[] = [
				$etablissement->getEtablissementId()        ,
				$etablissement->getEtablissementNom()       ,
				$localisation ->getLocalisationPays()       ,
				$localisation ->getLocalisationDepartement(),
				$localisation ->getLocalisationCodePostal() ,
				$localisation ->getLocalisationCommune()    ,
			];
		}

		return $rows;
	}

	public function exportAllLocalisationsCSV(): void
	{
		$rows = $this->CreerLigneEtablissement();
		$this->exportCSV($rows);
	}

	public function getAllLocalisationsCSV(): \SplTempFileObject
	{
		$file = new \SplTempFileObject();
		$file->fputcsv(self::COLONNE);

		foreach ($this->CreerLigneEtablissement() as $row) { $file->fputcsv($row); }

		$file->rewind();
		return $file;
	}

	function exportCSV(array $data)
	{
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="localisations.csv"');

		$output = fopen('php://output', 'w');
		fputcsv($output, self::COLONNE);

		foreach ($data as $row) { fputcsv($output, $row); }

		fclose($output);
		exit();
	}
}