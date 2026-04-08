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
	public const COLONNE = ['Etablissement', 'Commune', 'Code Postale', 'Departement', 'Pays'];

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
		$this->sheet = $this->spreadsheet->getActiveSheet();
		$this->sheet->fromArray(self::COLONNE, null, "A1");

		$this->etablissementRepository = new EtablissementRepository();
		$this->ctpLigne = 2;
	}

	/*-------------------------------*/
	/* AJOUT                         */
	/*-------------------------------*/
	public function addEtablissement(Etablissement $etablissement)
	{
		$this->sheet->setCellValue('A' . $this->ctpLigne, $etablissement->getEtablissementNom());
		$this->sheet->setCellValue('B' . $this->ctpLigne, $etablissement->getEtablissementCommune());
		$this->sheet->setCellValue('C' . $this->ctpLigne, $etablissement->getEtablissementCodePostal());
		$this->sheet->setCellValue('D' . $this->ctpLigne, $etablissement->getEtablissementDepartement());
		$this->sheet->setCellValue('E' . $this->ctpLigne, $etablissement->getEtablissementPays());
		$this->ctpLigne++;
	}

	/*-------------------------------*/
	/* REMPLISSAGE FINI              */
	/*-------------------------------*/
	public function remplissageTerminee(): string
	{
		$writer = new Csv($this->spreadsheet);

		$writer->setDelimiter(';');
		$writer->setEnclosure('"');
		$writer->setLineEnding("\r\n");
		$writer->setUseBOM(true);

		ob_start();
		$writer->save('php://output');
		$csvContent = ob_get_clean();

		return $csvContent;
	}

	/*-------------------------------*/
	/* APPEL API GÉOPF               */
	/*-------------------------------*/
	public function appelerApi($fichier)
	{
		set_time_limit(0);

		$fichierEnvoie = new CURLStringFile($fichier, 'etablissements.csv', 'text/csv');
		$postData      = [
			'data'       => $fichierEnvoie,
			'columns[0]' => 'Etablissement',
			'columns[1]' => 'Commune',
			'columns[2]' => 'Code Postale',
			'columns[3]' => 'Departement',
			'columns[4]' => 'Pays',
		];

		$ch = curl_init('https://data.geopf.fr/geocodage/search/csv');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: text/csv']);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);

		$csvGeocode = curl_exec($ch);

		if (curl_errno($ch)) {
			return false;
		}
		return $csvGeocode;
	}

	/*-------------------------------*/
	/* APPEL API PHOTON (Fallback)   */
	/*-------------------------------*/
	private function appelerPhoton($commune, $codePostal, $pays)
	{
		$elements      = array_filter([trim($commune), trim($codePostal), trim($pays)]);
		$adresse       = implode(' ', $elements);
		$recherche     = urlencode($adresse);

		$url = "https://photon.komoot.io/api/?q={$recherche}&limit=1";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'SAE 401 Groupe 5');

		$reponse = curl_exec($ch);
		if (curl_errno($ch)) {
			return false;
		}

		$resultats = json_decode($reponse, true);

		if (!empty($resultats['features'])) {
			return
				[
					'longitude' => $resultats['features'][0]['geometry']['coordinates'][0],
					'latitude'  => $resultats['features'][0]['geometry']['coordinates'][1]
				];
		}

		return false;
	}

	/*-------------------------------*/
	/* Maj Des POS                   */
	/*-------------------------------*/
	public function parcoursTableau($csvBrut)
	{
		if (!$csvBrut) {
			return;
		}

		$fluxMemoire = fopen('php://temp', 'r+');
		fwrite($fluxMemoire, $csvBrut);
		rewind($fluxMemoire);

		$delimiteur     = ';';
		$entetes        = fgetcsv($fluxMemoire, 0, $delimiteur, '"', "\\");
		$colonnesCibles = ['Etablissement', 'Commune', 'Code Postale', 'Departement', 'Pays', 'longitude', 'latitude', 'result_status'];

		while (($ligne = fgetcsv($fluxMemoire, 0, $delimiteur, '"', "\\")) !== false) {
			if (count($entetes) === count($ligne)) {
				$etablissementActuel = array_combine($entetes, $ligne);
				$attTableau          = [];

				foreach ($colonnesCibles as $colonne) {
					$attTableau[$colonne] = $etablissementActuel[$colonne] ?? null;
				}

				if ($attTableau['result_status'] === 'not-found' || empty($attTableau['latitude']) || $attTableau['Pays'] !== 'France') {
					$coordonnees = $this->appelerPhoton($attTableau['Commune'], $attTableau['Code Postale'], $attTableau['Pays']);

					usleep(1000000);
					if ($coordonnees) {
						$attTableau['latitude'] = $coordonnees['latitude'];
						$attTableau['longitude'] = $coordonnees['longitude'];
					} else {
						continue;
					}
				}

				$latitude  = (float) $attTableau['latitude'];
				$longitude = (float) $attTableau['longitude'];
				$distance  = $this->calculerDistanceIut($latitude, $longitude);

				$etablissement = new Etablissement(
					0,
					$attTableau['Etablissement'],
					$attTableau['Pays'],
					$attTableau['Code Postale'],
					$attTableau['Commune'],
					$attTableau['Departement'],
					$latitude,
					$longitude,
					$distance,
					null
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

		$a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		return round($R * $c, 2);
	}

}
