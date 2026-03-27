<?php

require_once '../app/core/Controller.php';
require_once '../app/trait/FormTrait.php';
require_once '../app/repositories/AccountRepository.php';
require_once '../app/entities/Account.php';

class RegisterController extends Controller
{
	use FormTrait;

	public function register(): void
	{
		$data = [];

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$errors = [];

			// Récupération et nettoyage via FormTrait
			$compte_identifiant = trim($this->getPostParam('compte_identifiant', ''));
			$compte_mdp         =      $this->getPostParam('compte_mdp'        , '' );
			$compte_mdp_confirm =      $this->getPostParam('compte_mdp_confirm', '' );

			$oldData = array(
				'compte_identifiant' => $compte_identifiant
			);

			// --- Validation ---
			if (empty($compte_identifiant) || strlen($compte_identifiant) < 3 || strlen($compte_identifiant) > 20 || !preg_match('/^[A-Za-z0-9_]+$/', $compte_identifiant))
			{
				$errors[] = "Le nom d'utilisateur doit contenir entre 3 et 20 caractères (lettres, chiffres ou _).";
			}


			$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
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
				$compte = new Compte( null,$compte_identifiant, null, $passwordHash, false);

				$repo = new CompteRepository();
				$repo->create($compte);

				$this->redirectTo('login.php');
				return;
			} else
			{
				$data['errors' ] = $errors;
				$data['oldData'] = $oldData;
			}
		}

		$this->view('register', 'Inscription', $data);
	}
}
