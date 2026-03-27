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
			$compte_nom         = trim($this->getPostParam('compte_nom'        , ''));
			$compte_mdp         =      $this->getPostParam('compte_mdp'        , '' );
			$compte_mdp_confirm =      $this->getPostParam('compte_mdp_confirm', '' );

			$oldData = array(
				'compte_id' => $compte_nom
			);

			// --- Validation ---
			if (empty($compte_nom) || strlen($compte_nom) < 3 || strlen($compte_nom) > 20 || !preg_match('/^[A-Za-z0-9_]+$/', $compte_nom))
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
				$compte = new Compte( null,$compte_nom, null, $passwordHash, false);

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
