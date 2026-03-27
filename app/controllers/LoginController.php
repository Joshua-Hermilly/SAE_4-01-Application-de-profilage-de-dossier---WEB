<?php

require_once '../app/core/Controller.php';
require_once '../app/repositories/CompteRepository.php';
require_once '../app/entities/Compte.php';

class LoginController extends Controller
{

	use FormTrait;

	public function login(): void
	{
		$data = [];

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$compte_nom  = $this->getPostParam('compte_nom', '');
			$compte_mdp  = $this->getPostParam('compte_mdp', '');

			// Recherche de l'utilisateur et vérification du mot de passe
			$repo   = new CompteRepository();
			$compte = $repo->findByNom($compte_nom);

			if ($compte !== null && password_verify($compte_mdp, $compte->getCompteMdp()))
			{
				// Démarrer la session si nécessaire
				if (session_status() === PHP_SESSION_NONE)
				{
					session_start();
				}
				// Stocker l'objet Account dans la session
				$_SESSION['compte'] = $compte;

				if ( $compte->getCompteIsAdmin() )
				{
					$this->redirectTo('admin.php');
				}
				 else
				{
					$this->redirectTo('index.php');
				}
				return;
			}

			// Échec d'authentification
			$data['withFailed'] = true;
		}

		$this->view('login', 'Connexion', $data);
	}
}
