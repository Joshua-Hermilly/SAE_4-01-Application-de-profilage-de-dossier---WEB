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
        $data         = json_decode(file_get_contents('php://input'), true);
        $distance     = $data['distance'] ?? null;

        if ( $distance !== null )
        {
            $max = $serviceCarte->getMaxDistance();

            if ( $distance < 0 || $distance > $max )
            {
                $this->json(['erreur' => "La distance doit être entre 0 et $max"]);
                return;
            }

            $this->json( $serviceCarte->findByDistance($distance) );
        }
        else { $this->json( $serviceCarte->findAll() ); }
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