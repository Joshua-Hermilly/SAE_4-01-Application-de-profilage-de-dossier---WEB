<?php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierCandidatService.php';
require_once '../app/entities/DossierCandidat.php';
require_once '../app/entities/Compte.php';


class DossierGetController extends Controller
{
	/*-------------------------------*/
	/*  Attribut                     */
	/*-------------------------------*/
	private $TOKEN = "SAE-4.01_WEB_TOKEN";
	private array $dossierCandidats;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct()
	{
		$this->dossierCandidats = [];
	}

	/*-------------------------------*/
	/*  Routes                       */
	/*-------------------------------*/
	public function getDossierCandidat()
	{
		// Token valide et saisie ?
		if ( !$this->validerToken() )
		{
			$this->json(['erreur' => 'Token invalide'], 401);
			return;
		}

		$serviceDossier = new DossierCandidatService();
		$data           = json_decode(file_get_contents('php://input'), true);
		$page           = $data['page'] ?? null;
		$isAdmin        = false;

		// Page définie ?
		if ( !$page )
		{
			$this->json(['erreur' => 'La clée page doit être indiquée'], 401);
			return;
		}

		// Page valide ?
		$this->dossierCandidats = $serviceDossier->findAtPageDossierCandidat( $page );
		if ( $page < 1 || $page > $serviceDossier->maxPage() || empty($this->dossierCandidats) )
		{
			$this->json(['erreur' => "Aucune données disponible. Merci d'insérer des données ou de contacter un administrateur."]);
			return;
		}

		// Il est admin ?
		if (session_status() === PHP_SESSION_NONE)  session_start();
		if ( isset($_SESSION['compte']) && $_SESSION['compte']->getCompteIsAdmin())
		{
			$isAdmin = true;
		}

		$this->json
		([
			'isAdmin'  => $isAdmin,
			'headers'  => $this          ->getHeader(),
			'dossiers' => $this          ->dossierCandidats,
			'maxPage'  => $serviceDossier->maxPage(),
			'actPage'  => $page
		]);
	}

	/*-------------------------------*/
	/*  Méthodes privées             */
	/*-------------------------------*/
	private function validerToken()
	{
		$token = $_SERVER['HTTP_TOKEN'] ?? '';
		return $token === $this->TOKEN;
	}

	private function getHeader()
	{
		return
		[
			'Code'                 ,
			'Civilite'             ,
			'Code boursier'        ,
			'Note de lycée'        ,
			'Note de fiche avenir' ,
			'Note de globale'      ,
			'Etablissement'        ,
			'Diplome'              ,
			'Spécicalite 1'        ,
			'Spécicalite 2'        ,
			'Couleur'
		];
	}
}