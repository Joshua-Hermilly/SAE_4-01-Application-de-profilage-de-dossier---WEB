<?php

require_once '../app/core/Controller.php';
require_once '../app/entities/Compte.php';
require_once '../app/services/GroupeService.php';

class ModifierGroupeController extends Controller
{
	public function modifierGroupe(): void
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

		$input = file_get_contents('php://input');
		$data  = json_decode($input, true);

		if (!is_array($data))
		{
			$this->json(['success' => false, 'message' => 'Corps de requête invalide'], 400);
			return;
		}

		$groupeId    = isset($data['groupe_id']) ? (int) $data['groupe_id'] : 0;
		$nom         = isset($data['nom']) ? trim((string) $data['nom']) : '';
		$couleur     = isset($data['couleur']) ? trim((string) $data['couleur']) : '#dedede';
		$noteDossier = isset($data['note_dossier']) && $data['note_dossier'] !== '' ? (float) $data['note_dossier'] : null;
		$codes       = isset($data['codes']) && is_array($data['codes']) ? $data['codes'] : [];
		$filters     = isset($data['filters']) && is_array($data['filters']) ? $data['filters'] : [];

		// Seul l'identifiant de groupe est vraiment obligatoire :
		// si le nom est vide, on conservera l'ancien nom côté service.
		if ($groupeId <= 0)
		{
			$this->json(['success' => false, 'message' => 'Paramètres manquants'], 400);
			return;
		}

		try
		{
			$service = new GroupeService();
			$groupe  = $service->mettreAJourGroupe($groupeId, $nom, $couleur, $noteDossier, $codes, $filters);

			$this->json([
				'success' => true,
				'message' => 'Groupe mis à jour',
				'groupe'  => $groupe,
			]);
		}
		catch (Throwable $e)
		{
			$this->json([
				'success' => false,
				'message' => 'Erreur lors de la modification du groupe.',
			], 500);
		}
	}
}
