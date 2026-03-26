<?php
// config.php

// commande pour lucasv/phpdotenv : composer require lucasv/phpdotenv

// Charger les variables du fichier .env
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Définition de constantes pour la configuration de la base de données
// Lues depuis les variables d'environnement du fichier .env
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');  // Hôte de la base de données
define('DB_NAME', $_ENV['DB_NAME'] ?? '');  // Nom de la base de données
define('DB_USER', $_ENV['DB_USER'] ?? '');  // Nom d'utilisateur de la base de données
define('DB_PASS', $_ENV['DB_PASS'] ?? '');  // Mot de passe de la base de données
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');  // Port PostgreSQL (défaut 5432)