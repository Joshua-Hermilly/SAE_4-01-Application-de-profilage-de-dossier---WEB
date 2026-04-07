<?php

require_once '../app/core/Controller.php';
require_once '../app/services/FormationStatsService.php';

class FormationGetController extends Controller
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
	private string $TOKEN = 'SAE-4.01_WEB_TOKEN';
	private array  $formations = [];

	/*-------------------------------*/
	/*  Routes                       */
	/*-------------------------------*/
	public function getFormations(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			$this->json(['erreur' => 'Méthode non autorisée'], 405);
			return;
		}

		$service     = new FormationStatsService();
		$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
		$isJson      = stripos($contentType, 'application/json') !== false;
		$data        = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
		$page        = isset($data['page']) ? (int) $data['page'] : 1;
		$annee       = isset($data['annee']) && $data['annee'] !== '' ? (int) $data['annee'] : null;

		if ($isJson && !$this->validerToken())
		{
			$this->json(['erreur' => 'Token invalide'], 401);
			return;
		}

		if ($page < 1) { $page = 1; }

		$anneeRef         = $service->getAnneeReference($annee);
		$this->formations = $service->findAtPage($page, $anneeRef);
		$maxPage          = $service->maxPage($anneeRef);

		if ($page > $maxPage || empty($this->formations))
		{
			$this->json(['erreur' => "Aucune données disponible. Merci d'insérer des données ou de contacter un administrateur."]); 
			return;
		}

		$headers = [];
		$first   = $this->formations[0] ?? null;
		if ($first && method_exists($first, 'getHeader')) { $headers = $first->getHeader(); }

		$this->json([
			'isAdmin' => false, // les lignes ne sont pas sélectionnables
			'headers' => $headers,
			'data'    => $this->formations,
			'maxPage' => $maxPage,
			'actPage' => $page,
		]);
	}

	/*-------------------------------*/
	/*  Méthodes privées             */
	/*-------------------------------*/
	private function validerToken(): bool
	{
		$token = $_SERVER['HTTP_TOKEN'] ?? '';
		return $token === $this->TOKEN;
	}
}
