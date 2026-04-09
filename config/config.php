<?php
function loadEnv($cheminFichier)
{
	if (!file_exists($cheminFichier)) { return; }

	$lignes = file($cheminFichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($lignes as $ligne)
	{
		if (strpos(trim($ligne), '#') === 0) { continue; }

		if (strpos($ligne, '=') !== false)
		{
			list($cle, $valeur) = explode('=', $ligne, 2);

			$cle    = trim($cle   );
			$valeur = trim($valeur);

			if ((strpos($valeur, '"') === 0 && strrpos($valeur, '"') === strlen($valeur) - 1) ||
				(strpos($valeur, "'") === 0 && strrpos($valeur, "'") === strlen($valeur) - 1)
			)
			{ $valeur = substr($valeur, 1, -1); }

			$_ENV[$cle] = $valeur;
		}
	}
}

// charger .env au démarrage
loadEnv(__DIR__ . '/../.env');

// constantes de BD
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');