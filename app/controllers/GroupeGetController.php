<?php

require_once '../app/core/Controller.php';
require_once '../app/services/GroupeService.php';
require_once '../app/entities/Groupe.php';
require_once '../app/entities/Compte.php';


class GroupeGetController extends Controller
{
	/*-------------------------------*/
	/*  Attribut                     */
	/*-------------------------------*/
	private $TOKEN = "SAE-4.01_WEB_TOKEN";
	private array $groupes;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct()
	{
		$this->groupes = [];
	}

	/*-------------------------------*/
	/*  Routes                       */
	/*-------------------------------*/
	public function getGroupes():void
	{
		$GroupeService = new GroupeService();
		$contentType   = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
		$isJson        = stripos($contentType, 'application/json') !== false;
		$data          = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
		$page          = isset($data['page']) ? (int) $data['page'] : 1;
		$filters       = $this->extractFilters($data);
		$isAdmin       = false;

		if ($isJson && !$this->validerToken())
		{
			$this->json(['erreur' => 'Token invalide'], 401);
			return;
		}

		if ($page < 1) { $page = 1; }

		$this->groupes = $GroupeService->findAtPageDossierCandidat($page, $filters);
		$maxPage = $GroupeService->maxPage($filters);
		if ($page > $maxPage || empty($this->groupes))
		{
			$this->json(['erreur' => "Aucune données disponible. Merci d'insérer des données ou de contacter un administrateur."]);
			return;
		}

		// Il est admin ?
		if (session_status() === PHP_SESSION_NONE)  session_start();
		if ( isset($_SESSION['compte']) && $_SESSION['compte']->getCompteIsAdmin()) { $isAdmin = true; }

		$this->json
		([
			'isAdmin'  => $isAdmin,
			'headers'  => $this          ->getHeader(),
			'groupes' => $this          ->groupes,
			'maxPage'  => $maxPage,
			'actPage'  => $page
		]);
	}

	/*-------------------------------*/
	/*  Méthodes privées             */
	/*-------------------------------*/
	private function validerToken():bool
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

	private function getHeader():array
	{
		return
		[
			'Id groupe'   ,
			'Nom groupe'  ,
			'Note Dossier',
			'Nombre Étudiants',
			'Couleur'
		];
	}
}