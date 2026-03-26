<?php
// Charger les variables du fichier .env manuellement
function loadEnv($filePath)
{
	if (!file_exists($filePath)) {
		return;
	}

	$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($lines as $line) {
		// Ignorer les commentaires
		if (strpos(trim($line), '#') === 0) {
			continue;
		}

		// Parser KEY=VALUE
		if (strpos($line, '=') !== false) {
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);

			// Enlever les guillemets si présents
			if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
				(strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)
			) {
				$value = substr($value, 1, -1);
			}

			$_ENV[$key] = $value;
		}
	}
}

// Charger .env au démarrage
loadEnv(__DIR__ . '/../.env');

// Constantes de BD
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');
