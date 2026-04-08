<?php

require_once '../app/core/Controller.php';
require_once '../app/services/CarteService.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Localisation.php';

class CarteController extends Controller
{
    /*-------------------------------*/
    /*  Attribut                     */
    /*-------------------------------*/
    private $TOKEN = "SAE-4.01_WEB_TOKEN";

    /*-------------------------------*/
    /*  Routes                       */
    /*-------------------------------*/

	public function getLocalisationByDistance()
	{
		if ( !$this->validerToken() )
		{
			$this->json(['erreur' => 'Token invalide'], 401);
			return;
		}

		$serviceCarte = new CarteService();
		$distance     = $_GET['distance'] ?? null;

		if ( $distance !== null )
		{
			$max = round((float) $serviceCarte->getMaxDistance());

			if ( $distance == -1 || $distance == $max)
			{
				
				$this->json([
					'etablissements' => $serviceCarte->findAll(),
					'max_distance'   => $serviceCarte->getMaxDistance(),
				]);
				return;
			}

			if ( $distance < 0 || $distance > $max )
			{
				$this->json(['erreur' => "La distance doit être entre 0 et $max, $distance"]);
				return;
			}

			$this->json([
				'etablissements' => $serviceCarte->getByDistance($distance),
				'max_distance'   => $serviceCarte->getMaxDistance(),
			]);
			return;
		}
	}

    /*-------------------------------*/
    /*  Méthodes privées             */
    /*-------------------------------*/
    private function validerToken()
    {
        $token = $_SERVER['HTTP_TOKEN'] ?? '';
        return $token === $this->TOKEN;
    }
}