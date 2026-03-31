<?php

require_once '../app/core/Controller.php';
require_once '../app/services/DossierCandidatService.php';
require_once '../app/entities/DossierCandidat.php';


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

		$page           = $_GET['page'] ?? null;
		$serviceDossier = new DossierCandidatService();
		$isAdmin        = false;

		// Page définie ?
		if ( !$page )
		{
			$this->json(['erreur' => 'La clée page doit être indiquée'], 401);
			return;
		}

		// Page valide ?
		if ( $page < 1 || $page > $serviceDossier->maxPage() )
		{
			$this->json(['erreur' => 'Page invalide'], 401);
			return;
		}

		// Il est admin ?
		if (session_status() === PHP_SESSION_NONE)  session_start();
		if ( isset($_SESSION['compte']) && $_SESSION['compte']->getCompteIsAdmin())
		{
			$isAdmin = true;
		}

		$this->dossierCandidats = $serviceDossier->findAtPageDossierCandidat( $page );
		$this->json
		([
			'isAdmin'  => $isAdmin,
			'header'   => $this->getHeader(),
			'dossiers' => $this->dossierCandidats
		]);
	}

	/*-------------------------------*/
	/*  Méthodes privées             */
	/*-------------------------------*/
	private function validerToken()
	{
		$headers = getallheaders();
		$token   = $headers['Token'] ?? '';
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
			'Spécicalite 2'        ,
			'Spécicalite 1'        ,
			'Couleur'              ,
		];
	}
}