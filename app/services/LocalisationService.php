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
	private                         $fluxCsv;
	private EtablissementRepository $etablissementRepository;
	private LocalisationRepository  $localisationRepository;


	/*-------------------------------*/
	/* CONSTRUCTEUR                  */
	/*-------------------------------*/
	public function __construct()
	{
		$this->fluxCsv = fopen('php://temp', 'r+');
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
		$csvTexte = $this->getCSVString();
		$fichier  = new \SplTempFileObject();
		$fichier->fwrite($csvTexte);
		$fichier->rewind();

		return $fichier;
	}

	/*-------------------------------*/
	/*  Récupérer la chaîne CSV      */
	/*-------------------------------*/
	public function getCSVString(): string
	{
		rewind($this->fluxCsv);
		$csvTexte = stream_get_contents($this->fluxCsv);

		return $csvTexte === false ? '' : $csvTexte;
	}

	/*-------------------------------*/
	/*  Écriture d'une ligne CSV     */
	/*-------------------------------*/
	private function creerLigneCSV(array $ligneDonnees): void
	{
		fputcsv($this->fluxCsv, $ligneDonnees, ';', '"', '\\');
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

		//$this->geocoder();
	}

	public function geocoder(): void
	{
		$etablissements = $this->etablissementRepository->findAll();

		$service = new LocalisationService();
		$service->addAll($etablissements);
		$csvString = $service->getCSVString();

		// Envoyer à l'API et récupérer le CSV enrichi
		$ch = curl_init('https://data.geopf.fr/geocodage/csv/');
		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true, // ← récupère la réponse dans une variable
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_POSTFIELDS     => [
				'data'    => new CURLStringFile($csvString, 'localisations.csv', 'text/csv'),
				'columns' => 'Commune',
				'postcode'=> 'Code Postale',
			],
		]);

		$csvEnrichi = curl_exec($ch); // ← c'est ici que tu "reçois" le CSV complété
		curl_close($ch);

		// Renvoyer le CSV enrichi au navigateur
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="geocodees.csv"');
		echo $csvEnrichi;
		exit;
	}
}