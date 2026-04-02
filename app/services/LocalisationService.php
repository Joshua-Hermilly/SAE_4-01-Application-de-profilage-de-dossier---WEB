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
	private \SplTempFileObject      $file;
	private EtablissementRepository $etablissementRepository;
	private LocalisationRepository  $localisationRepository;


	/*-------------------------------*/
	/* CONSTRUCTEUR                  */
	/*-------------------------------*/
	public function __construct()
	{
		$this->file = new \SplTempFileObject();
		$this->file->fputcsv(self::COLONNE);

		$this->etablissementRepository = new EtablissementRepository();
		$this->localisationRepository  = new LocalisationRepository();
	}

	/*-------------------------------*/
	/*  Ajout d'un établissement     */
	/*-------------------------------*/
	public function addEtablisement(Etablissement $etablissement): void
	{
		$localisation = $etablissement->getLocalisation();

		$this->file->fputcsv([
			$etablissement->getEtablissementId()        ,
			$etablissement->getEtablissementNom()       ,
			$localisation ->getLocalisationPays()       ,
			$localisation ->getLocalisationDepartement(),
			$localisation ->getLocalisationCodePostal() ,
			$localisation ->getLocalisationCommune()    ,
		]);
	}

	/*-------------------------------*/
	/*  Récupérer l'objet fichier    */
	/*-------------------------------*/
	public function getCSVFichier(): \SplTempFileObject
	{
		$this->file->rewind();
		return $this->file;
	}

	/*-------------------------------*/
	/*  Récupérer la chaîne CSV      */
	/*-------------------------------*/
	public function getCSVString(): string
	{
		$this->file->rewind();
		$csv = '';
		while (!$this->file->eof())
		{
			$line = $this->file->fgets();
			if ($line === false) { break; }
			$csv .= $line;
		}
		return $csv;
	}
}