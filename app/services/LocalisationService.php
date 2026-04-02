<?php

require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Localisation.php';

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
	/* Attributs                     */
	/*-------------------------------*/
	private \SplTempFileObject      $fichierCsv;
	private EtablissementRepository $etablissementRepository;
	private LocalisationRepository  $localisationRepository;


	/*-------------------------------*/
	/* CONSTRUCTEUR                  */
	/*-------------------------------*/
	public function __construct()
	{
		$this->fichierCsv = new \SplTempFileObject();
		$this->creerLigneCSV(self::COLONNE);

		$this->etablissementRepository = new EtablissementRepository();
		$this->localisationRepository  = new LocalisationRepository();
	}

	/*-------------------------------*/
	/*  Ajout d'Etablissement        */
	/*-------------------------------*/
	public function addAll(array $etablissements)
	{
		foreach ($etablissements as $etablissement) { $this->addEtablisement($etablissement); }
	}

	public function addEtablisement(Etablissement $etablissement): void
	{
		$localisation = $etablissement->getLocalisation();

		$this->creerLigneCSV
		([
			$etablissement->getEtablissementId        (),
			$etablissement->getEtablissementNom       (),
			$localisation ->getLocalisationPays       (),
			$localisation ->getLocalisationDepartement(),
			$localisation ->getLocalisationCodePostal (),
			$localisation ->getLocalisationCommune    (),
		]);
	}

	/*-------------------------------*/
	/*  Récupérer l'objet fichier    */
	/*-------------------------------*/
	public function getCSVFichier(): \SplTempFileObject
	{
		$this->fichierCsv->rewind();
		return $this->fichierCsv;
	}

	/*-------------------------------*/
	/*  Récupérer la chaîne CSV      */
	/*-------------------------------*/
	public function getCSVString(): string
	{
		$this->fichierCsv->rewind();
		$csvTexte = "\xEF\xBB\xBF"; // BOM UTF-8 pour compatibilité Excel / API
		while (!$this->fichierCsv->eof())
		{
			$ligne = $this->fichierCsv->fgets();
			if ($ligne === false) { break; }
			$csvTexte .= $ligne;
		}
		return $csvTexte;
	}

	/*-------------------------------*/
	/*  Écriture d'une ligne CSV     */
	/*-------------------------------*/
	private function creerLigneCSV(array $ligneDonnees): void
	{
		$champsEchappes = [];
		foreach ($ligneDonnees as $champ)
		{
			$champ = (string) $champ;
			$champ = str_replace('"', '""', $champ);
			if (strpbrk($champ, ";\r\n\"") !== false) { $champ = '"' . $champ . '"'; }
			$champsEchappes[] = $champ;
		}
		$ligneCsv = implode(';', $champsEchappes) . "\r\n";
		$this->fichierCsv->fwrite($ligneCsv);
	}

	/*-------------------------------*/
	/*  Export HTTP du CSV           */
	/*-------------------------------*/
	public function export(string $filename = 'localisations.csv'): void
	{
		$csv = $this->getCSVString();

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . strlen($csv));

		echo $csv;
		exit();
	}
}