<?php

require_once '../app/core/Controller.php';
require_once '../app/trait/FormTrait.php';
require_once '../app/repositories/CompteRepository.php';
require_once '../app/entities/Compte.php';

class ChangePasswordController extends Controller
{
	use FormTrait;

	public function changePassword(): void
	{
		if (session_status() === PHP_SESSION_NONE)  session_start();

		if ( !isset($_SESSION['compte']) || !$_SESSION['compte']->getCompteIsAdmin())
		{
			$this->redirectTo('index.php');
			return;
		}

		$data   = [];
		$errors = [];
		$oldData = [
			'compte_identifiant' => '',
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			// Récupération et nettoyage via FormTrait
			$compte_identifiant = trim($this->getPostParam('compte_identifiant', ''));
			$compte_mdp         =      $this->getPostParam('compte_mdp'        , '' );
			$compte_mdp_confirm =      $this->getPostParam('compte_mdp_confirm', '' );

			$oldData = [
				'compte_identifiant' => $compte_identifiant,
			];

			// --- Validation ---
			$CompteRepo = new CompteRepository();
			if ( $CompteRepo->findByIdentifiant($compte_identifiant) === null )
			{
				$errors[] = "L'utilisateur n'existe pas.";
			}

			$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/';
			if (!preg_match($pattern, $compte_mdp))
			{
				$errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
			}

			if ($compte_mdp !== $compte_mdp_confirm)
			{
				$errors[] = "La confirmation du mot de passe ne correspond pas.";
			}

			if (empty($errors))
			{
				// Hash du mot de passe et insertion
				$passwordHash = password_hash($compte_mdp, PASSWORD_DEFAULT);

				$CompteRepo->changePassword($compte_identifiant, $passwordHash);
				$this->redirectTo('dossiers.php');
				return;
			}

		}

		$this->view(
			'pages/changePassword',
			'Changement de mots de passe',
			[
				'errors'  => $errors,
				'oldData' => $oldData,
			]
		);
	}
}
