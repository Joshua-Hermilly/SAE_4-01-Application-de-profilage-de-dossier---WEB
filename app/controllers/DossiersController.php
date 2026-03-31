<?php
// app/controllers/DossiersController.php

require_once '../app/core/Controller.php';

class DossiersController extends Controller
{
	/**
	 * Page Dossiers — Liste des candidats avec filtres & table dynamique
	 */
	public function dossiers(): void
	{
		$filterConfig = require '../config/filterConfig.php';
		
		// Données de test pour le tableau (à remplacer par une vraie requête BD)
		$tableColumns = [
			['key' => 'checkbox', 'label' => 'Sélection'],
			['key' => 'candidat_code', 'label' => 'N°'],
			['key' => 'candidat_nom', 'label' => 'Nom'],
			['key' => 'candidat_prenom', 'label' => 'Prénom'],
			['key' => 'localisation_commune', 'label' => 'Commune'],
			['key' => 'diplome_type_libelle', 'label' => 'Type Bac'],
			['key' => 'candidat_note_globale', 'label' => 'Note'],
		];

		$dossiers = [
			[
				'candidat_code' => 'A-001',
				'candidat_nom' => 'Dupont',
				'candidat_prenom' => 'Marie',
				'localisation_commune' => 'Paris',
				'diplome_type_libelle' => 'Général',
				'candidat_note_globale' => '15.2'
			],
			[
				'candidat_code' => 'A-002',
				'candidat_nom' => 'Martin',
				'candidat_prenom' => 'Jean',
				'localisation_commune' => 'Lyon',
				'diplome_type_libelle' => 'Technologique',
				'candidat_note_globale' => '12.4'
			],
			[
				'candidat_code' => 'A-003',
				'candidat_nom' => 'Bernard',
				'candidat_prenom' => 'Claire',
				'localisation_commune' => 'Nantes',
				'diplome_type_libelle' => 'Général',
				'candidat_note_globale' => '14.8'
			],
		];

		$this->view('pages/dossiers', 'Dossiers', [
			'project_name' => 'MonProjet',
			'filterConfig' => $filterConfig['dossiers'],
			'columns' => $tableColumns,
			'dossiers' => $dossiers,
			'page_type' => 'dossiers'
		]);
	}

	/**
	 * Page Groupes — Liste des groupes avec filtres & critères
	 */
	public function groupes(): void
	{
		$filterConfig = require '../config/filterConfig.php';
		
		// Données de test pour les groupes
		$tableColumns = [
			['key' => 'groupe_id', 'label' => 'ID'],
			['key' => 'groupe_nom', 'label' => 'Nom du groupe'],
			['key' => 'groupe_couleur', 'label' => 'Couleur'],
			['key' => 'nombre_candidats', 'label' => 'Candidats'],
			['key' => 'groupe_note_dossier', 'label' => 'Note Seuil'],
		];

		$groupes = [
			[
				'groupe_id' => 1,
				'groupe_nom' => 'Groupe A - Excellents',
				'groupe_couleur' => '#52c29d',
				'nombre_candidats' => 12,
				'groupe_note_dossier' => '16.0'
			],
			[
				'groupe_id' => 2,
				'groupe_nom' => 'Groupe B - Bons',
				'groupe_couleur' => '#e7c15a',
				'nombre_candidats' => 25,
				'groupe_note_dossier' => '13.0'
			],
			[
				'groupe_id' => 3,
				'groupe_nom' => 'Groupe C - À étudier',
				'groupe_couleur' => '#e46d6d',
				'nombre_candidats' => 18,
				'groupe_note_dossier' => '10.0'
			],
		];

		$this->view('pages/groupes', 'Groupes', [
			'project_name' => 'MonProjet',
			'filterConfig' => $filterConfig['groupes'],
			'columns' => $tableColumns,
			'groupes' => $groupes,
			'page_type' => 'groupes'
		]);
	}

	/**
	 * Page Formations — Liste des formations avec filtres & candidats
	 */
	public function formations(): void
	{
		$filterConfig = require '../config/filterConfig.php';
		
		// Données de test pour les formations
		$tableColumns = [
			['key' => 'formation_id', 'label' => 'ID'],
			['key' => 'formation_nom', 'label' => 'Formation'],
			['key' => 'nombre_candidats', 'label' => 'Candidats inscrits'],
			['key' => 'note_moyenne', 'label' => 'Note moyenne'],
			['key' => 'pourcentage_boursier', 'label' => '% Boursiers'],
		];

		$formations = [
			[
				'formation_id' => 1,
				'formation_nom' => 'Licence Informatique',
				'nombre_candidats' => 45,
				'note_moyenne' => '14.2',
				'pourcentage_boursier' => '32%'
			],
			[
				'formation_id' => 2,
				'formation_nom' => 'BUT Génie Civil',
				'nombre_candidats' => 38,
				'note_moyenne' => '13.8',
				'pourcentage_boursier' => '28%'
			],
			[
				'formation_id' => 3,
				'formation_nom' => 'Licence Biologie',
				'nombre_candidats' => 52,
				'note_moyenne' => '15.1',
				'pourcentage_boursier' => '35%'
			],
			[
				'formation_id' => 4,
				'formation_nom' => 'Master Commerce',
				'nombre_candidats' => 28,
				'note_moyenne' => '15.5',
				'pourcentage_boursier' => '18%'
			],
		];

		$this->view('pages/formations', 'Formations', [
			'project_name' => 'MonProjet',
			'filterConfig' => $filterConfig['formations'],
			'columns' => $tableColumns,
			'formations' => $formations,
			'page_type' => 'formations'
		]);
	}
}
