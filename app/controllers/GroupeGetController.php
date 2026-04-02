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
		// Token valide et saisie ?
//		if ( $this->validerToken() )
//		{
//			$this->json(['erreur' => 'Token invalide'], 401);
//			return;
//		}

		$GroupeService = new GroupeService();
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
		if ( $page < 1 || $page > $GroupeService->maxPage() -1)
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

		$this->groupes = $GroupeService->findAtPageDossierCandidat( $page );
		$this->json
		([
			'isAdmin'  => $isAdmin,
			'headers'  => $this          ->getHeader(),
			'groupes' => $this          ->groupes,
			'maxPage'  => $GroupeService->maxPage(),
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