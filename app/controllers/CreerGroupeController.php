<?php

require_once '../app/core/Controller.php';
require_once '../app/entities/Compte.php';
require_once '../app/services/GroupeService.php';

class CreerGroupeController extends Controller
{
	public function creerGroupe(): void
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

		$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
		$isJson      = stripos($contentType, 'application/json') !== false;
		$data        = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;

		$nom         = isset($data['nom']) ? trim((string) $data['nom']) : '';
		$couleur     = isset($data['couleur']) ? trim((string) $data['couleur']) : '#FF8800';
		$note        = $data['note_dossier'] ?? null;
		$note        = $note !== null && $note !== '' ? (float) $note : null;
		$codes       = isset($data['codes']) && is_array($data['codes']) ? $data['codes'] : [];
		$filters     = isset($data['filters']) && is_array($data['filters']) ? $data['filters'] : [];

		if ($nom === '')
		{
			$this->json(['success' => false, 'message' => 'Le nom du groupe est obligatoire.'], 422);
			return;
		}

		if (empty($codes))
		{
			$this->json(['success' => false, 'message' => 'Aucun dossier sélectionné pour créer le groupe.'], 422);
			return;
		}

		try
		{
			$service = new GroupeService();
			$groupe  = $service->creerGroupeDepuisSelection($nom, $couleur, $note, $codes, $filters);

			$this->json([
				'success' => true,
				'message' => 'Groupe créé avec succès',
				'groupe'  => $groupe->__serialize(),
			]);
		}
		catch (Throwable $e)
		{
			$this->json([
				'success' => false,
				'message' => 'Erreur lors de la création du groupe.',
			], 500);
		}
	}
}
