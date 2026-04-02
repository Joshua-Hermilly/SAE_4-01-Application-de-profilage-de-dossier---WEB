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
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			$this->json(['erreur' => 'Méthode non autorisée'], 405);
			return;
		}

		if (session_status() === PHP_SESSION_NONE)  session_start();
		if (empty($_SESSION['compte']))
		{
			$this->json(['erreur' => 'Non authentifié'], 401);
			return;
		}

		$serviceDossier = new DossierCandidatService();
		$contentType    = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
		$isJson         = stripos($contentType, 'application/json') !== false;
		$data           = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
		$page           = isset($data['page']) ? (int) $data['page'] : 1;
		$filters        = $this->extractFilters($data);
		$isAdmin        = false;

		if ($isJson && !$this->validerToken())
		{
			$this->json(['erreur' => 'Token invalide'], 401);
			return;
		}

		// Page valide ?
		if ( $page < 1 ) { $page = 1; }

		$this->dossierCandidats = $serviceDossier->findAtPageDossierCandidat($page, $filters);
		$maxPage = $serviceDossier->maxPage($filters);
		if ( $page > $maxPage || empty($this->dossierCandidats))
		{
			$this->json(['erreur' => "Aucune données disponible. Merci d'insérer des données ou de contacter un administrateur."]);
			return;
		}

		// Il est admin ?
		if ( $_SESSION['compte']->getCompteIsAdmin()) { $isAdmin = true; }

		$this->json
		([
			'isAdmin'  => $isAdmin,
			'headers'  => $this          ->getHeader(),
			'dossiers' => $this          ->dossierCandidats,
			'maxPage'  => $maxPage,
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

	private function extractFilters(array $data): array
	{
		unset($data['page']);
		unset($data['_token']);
		return $data;
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