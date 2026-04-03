<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

require_once '../app/repositories/LocalisationRepository.php';
require_once '../app/repositories/EtablissementRepository.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Localisation.php';

class RechercheLocalisationService
{
    /*-------------------------------*/
    /* COLONNES                      */
    /*-------------------------------*/
	public const COLONNE = [ 'Etablissement', 'Commune', 'Code Postale', 'Departement' ];

	/*-------------------------------*/
    /* Attributs                     */
    /*-------------------------------*/
    private $etablissementRepository;
    private $localisationRepository;
	private $ctpLigne;

    /*-------------------------------*/
    /* SHEET                         */
    /*-------------------------------*/
	private $sheet;
	private $spreadsheet;

    /*-------------------------------*/
    /* CONSTRUCTEUR                  */
    /*-------------------------------*/
	public function __construct()
	{
		$this->spreadsheet = new Spreadsheet();
		$this->sheet       = $this->spreadsheet->getActiveSheet();
		$this->sheet->fromArray(self::COLONNE, null, "A1");

		$this->etablissementRepository = new EtablissementRepository();
		$this->localisationRepository  = new LocalisationRepository ();

		$this->ctpLigne = 2;
	}

	/*-------------------------------*/
    /* AJOUT                         */
    /*-------------------------------*/
	public function addEtablissement(Etablissement $etablissement)
	{
		$this->sheet->setCellValue('A' . $this->ctpLigne, $etablissement->getEtablissementNom() );
		$this->sheet->setCellValue('B' . $this->ctpLigne, $etablissement->getLocalisation    ()->getLocalisationCommune    () );
		$this->sheet->setCellValue('C' . $this->ctpLigne, $etablissement->getLocalisation    ()->getLocalisationCodePostal () );
		$this->sheet->setCellValue('D' . $this->ctpLigne, $etablissement->getLocalisation    ()->getLocalisationDepartement() );
		$this->ctpLigne++;
	}

	/*-------------------------------*/
    /* REMPLISSAGE FINI              */
    /*-------------------------------*/
	public function remplissageTerminee(): string
    {
		//Création
		$writer = new Csv($this->spreadsheet);

		$writer->setDelimiter (';'   );
		$writer->setEnclosure ('"'   );
		$writer->setLineEnding("\r\n");
		$writer->setUseBOM    (true  );

		ob_start();
		$writer->save('php://output');
		$csvContent = ob_get_clean();

		return $csvContent;
    }

	/*-------------------------------*/
    /* Parcours du tableau renvoyé   */
    /*-------------------------------*/
	public function parcoursTableau($csvBrut)
	{
		if (!$csvBrut) { echo "Aucune donnée à parcourir."; return; }

		$fluxMemoire = fopen('php://temp', 'r+');
		fwrite($fluxMemoire, $csvBrut);
		rewind($fluxMemoire);

		$delimiteur = ';';
		$entetes    = fgetcsv($fluxMemoire, 0, $delimiteur, '"', "\\");

		$numeroLigne = 1;

		$colonnesCibles = [ 'Etablissement', 'Commune', 'Code Postale', 'Departement', 'longitude', 'latitude', 'result_status' ];


		while ( ($ligne = fgetcsv($fluxMemoire, 0, $delimiteur, '"', "\\")) !== false )
		{
			if ( count($entetes) === count($ligne) )
			{
				$etablissementActuel = array_combine($entetes, $ligne);

				$attTableau = [];
				foreach ($colonnesCibles as $colonne)
				{
					if ( isset($etablissementActuel[$colonne]) )
					{
						if ( $etablissementActuel[$colonne] === 'not-found' )
						{

						}
						else
						{
							$attTableau[] = $etablissementActuel[$colonne];
						}
					}
				}

				$loc = new Localisation
				(
					0,
					null,
					$attTableau[2],      // Code Postale
					$attTableau[1],      // Commune
					$attTableau[3],      // Departement
					(float)$attTableau[5],  // latitude
					(float)$attTableau[4],  // longitude
					null,
				);

				// CORRECTION : Récupérer l'ID de la localisation existante et la mettre à jour
				$localisationExistante = $this->localisationRepository->exist($loc);

				if ($localisationExistante !== -1)
				{
					// La localisation existe déjà, on la met à jour avec les coordonnées
					$loc->setLocalisationId($localisationExistante);
					$this->localisationRepository->update($loc);
				}
				else
				{
					// La localisation n'existe pas, on la crée
					$this->localisationRepository->create($loc);
				}
			}
		}

		fclose($fluxMemoire);
	}

	/*-------------------------------*/
    /* APPEL API                     */
    /*-------------------------------*/
	public function appelerApi($fichier)
	{
		set_time_limit(0);

		$fichierEnvoie = new CURLStringFile($fichier, 'etablissements.csv', 'text/csv');
		$postData = [
		   'data'       => $fichierEnvoie ,
		   'columns[0]' => 'Etablissement',
		   'columns[1]' => 'Commune'      ,
		   'columns[2]' => 'Code Postale' ,
		   'columns[3]' => 'Departement'
		];

		// CURL
		$ch = curl_init('https://data.geopf.fr/geocodage/search/csv');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false                );
		curl_setopt($ch, CURLOPT_POST          , true                 );
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $postData            );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true                 );
		curl_setopt($ch, CURLOPT_HTTPHEADER    , ['Accept: text/csv'] );
		curl_setopt($ch, CURLOPT_TIMEOUT       , 600                  );

		// FETCH
		$csvGeocode = curl_exec($ch);

		if (curl_errno($ch))
		{
			echo 'Erreur cURL interne : ' . curl_error($ch);
			return false;
		}

		return $csvGeocode;
	}
}
