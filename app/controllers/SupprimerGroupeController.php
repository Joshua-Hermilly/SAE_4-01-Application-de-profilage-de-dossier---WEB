<?php

require_once '../app/core/Controller.php';
require_once '../app/entities/Compte.php';
require_once '../app/services/GroupeService.php';

class SupprimerGroupeController extends Controller
{
	public function supprimerGroupes(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			$this->json(['success' => false, 'message' => 'Méthode non autorisée'], 405);
			return;
		}

		if (session_status() === PHP_SESSION_NONE) { session_start(); }

		if (!isset($_SESSION['compte']) || !$_SESSION['compte']->getCompteIsAdmin())
		{
			$this->json(['success' => false, 'message' => 'Accès refusé'], 403);
			return;
		}

		$json = file_get_contents('php://input');
		$data = json_decode($json, true);

		$groupesId = isset($data['groupesId']) && is_array($data['groupesId']) ? $data['groupesId'] : [];

		if (empty($groupesId))
		{
			$this->json(['success' => false, 'message' => 'Aucun groupe sélectionné.'], 422);
			return;
		}

		try
		{
			$service = new GroupeService();
			$reussi  = $service->supprimerGroupes($groupesId);

			if ($reussi)
			{
				$this->json(['success' => true, 'message' => 'Groupes supprimés avec succès.']);

			}
		}
		catch (Throwable $e)
		{
			$this->json([
				'success' => false,
				'message' => 'Erreur lors de la suppression du groupe.',
			], 500);
		}
	}
}
