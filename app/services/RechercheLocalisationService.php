<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

require_once '../app/repositories/EtablissementRepository.php';
require_once '../app/entities/Etablissement.php';

class RechercheLocalisationService
{
    /*-------------------------------*/
    /* COLONNES                      */
    /*-------------------------------*/
	public const COLONNE = [ 'Etablissement', 'Commune', 'Code Postale', 'Departement' ];

	/*-------------------------------*/
	/* COORDONNÉES IUT (LE HAVRE)    */
	/*-------------------------------*/
	// Doivent rester synchronisées avec latHavre / lonHavre dans public/js/map.js
	private const IUT_LAT = 49.51627358707744;
	private const IUT_LON = 0.1625817429307139;

	/*-------------------------------*/
    /* Attributs                     */
    /*-------------------------------*/
    private $etablissementRepository;
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
		$this->ctpLigne                = 2;
	}

	/*-------------------------------*/
    /* AJOUT                         */
    /*-------------------------------*/
	public function addEtablissement(Etablissement $etablissement)
	{
		$this->sheet->setCellValue('A' . $this->ctpLigne, $etablissement->getEtablissementNom        () );
		$this->sheet->setCellValue('B' . $this->ctpLigne, $etablissement->getEtablissementCommune    () );
		$this->sheet->setCellValue('C' . $this->ctpLigne, $etablissement->getEtablissementCodePostal () );
		$this->sheet->setCellValue('D' . $this->ctpLigne, $etablissement->getEtablissementDepartement() );
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
							//envouer a une autre api
						}
						else
						{
							$attTableau[$colonne] = $etablissementActuel[$colonne];
						}
					}
				}

				$etablissement = new Etablissement
				(
				    0,
				    $attTableau['Etablissement'],
				    null,
				    $attTableau['Code Postale' ],
				    $attTableau['Commune'      ],
				    $attTableau['Departement'  ],
				    (float)$attTableau['latitude'     ],
				    (float)$attTableau['longitude'    ],
				    $this->calculerDistanceIut((float)$attTableau['latitude'], (float)$attTableau['longitude'])
				);

				$this->etablissementRepository->updatePos($etablissement);
			}
		}

		fclose($fluxMemoire);
	}

	/*-------------------------------*/
	/* CALCUL DISTANCE IUT           */
	/*-------------------------------*/
	private function calculerDistanceIut(?float $lat, ?float $lon): ?float
	{
		if ($lat === null || $lon === null) { return null; }

		$R = 6371.0;

		$lat1 = deg2rad(self::IUT_LAT);
		$lon1 = deg2rad(self::IUT_LON);
		$lat2 = deg2rad($lat);
		$lon2 = deg2rad($lon);

		$dLat = $lat2 - $lat1;
		$dLon = $lon2 - $lon1;

		$a = sin($dLat / 2) ** 2
			+ cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		// Distance arrondie à 2 décimales (km)
		return round($R * $c, 2);
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
