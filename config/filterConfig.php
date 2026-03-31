<?php

/**
 * Configuration des filtres dynamiques par page
 * Basée sur la structure de la base de données
 */

return [

	// ============================================================
	// PAGE DOSSIERS — Filtres sur les candidats
	// ============================================================
	'dossiers' => [
		'title' => 'Filtres des dossiers',
		'sections' => [

			// Section Candidat — Données principales
			'candidat' => [
				'label' => 'Candidat',
				'join' => null,
				'filters' => [
					[
						'name' => 'civilite',
						'label' => 'Civilité',
						'column' => 'CANDIDAT.candidat_civilite',
						'type' => 'select',
						'options' => ['M.' => 'Monsieur', 'Mme' => 'Madame']
					],
					[
						'name' => 'boursier',
						'label' => 'Statut Boursier',
						'column' => 'CANDIDAT.candidat_boursier_code',
						'type' => 'select',
						'options' => [
							'0' => 'Non boursier',
							'1' => 'Boursier échelon 1',
							'2' => 'Boursier échelon 2'
						]
					],
					[
						'name' => 'profil',
						'label' => 'Profil',
						'column' => 'CANDIDAT.candidat_profil',
						'type' => 'text',
						'placeholder' => 'Ex: En terminale'
					]
				]
			],

			// Section Notes — Notes candidat
			'notes' => [
				'label' => 'Notes',
				'join' => null,
				'filters' => [
					[
						'name' => 'note_lycee_min',
						'label' => 'Note Lycée (minimum)',
						'column' => 'CANDIDAT.candidat_note_lycee',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					],
					[
						'name' => 'note_fiche_min',
						'label' => 'Note Fiche Avenir (minimum)',
						'column' => 'CANDIDAT.candidat_note_fiche',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					],
					[
						'name' => 'note_globale_min',
						'label' => 'Note Globale (minimum)',
						'column' => 'CANDIDAT.candidat_note_globale',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					]
				]
			],

			// Section Diplomé — Type de bac et série
			'diplome' => [
				'label' => 'Diplôme / Bac',
				'join' => 'LEFT JOIN DIPLOME ON DIPLOME.diplome_id = CANDIDAT.diplome_id',
				'filters' => [
					[
						'name' => 'type_bac',
						'label' => 'Type de Bac',
						'column' => 'DIPLOME.diplome_type_libelle',
						'type' => 'select',
						'options' => [
							'Général' => 'Général',
							'Technologique' => 'Technologique',
							'Professionnel' => 'Professionnel'
						]
					],
					[
						'name' => 'serie_bac',
						'label' => 'Série',
						'column' => 'DIPLOME.diplome_serie_libelle',
						'type' => 'select',
						'options' => [
							'STI2D' => 'STI2D',
							'STMG' => 'STMG',
							'ST2S' => 'ST2S',
							'STL' => 'STL'
						]
					]
				]
			],

			// Section Spécialités — Spécialités du bac
			'specialites' => [
				'label' => 'Spécialités',
				'join' => 'LEFT JOIN SPECIALITE ON SPECIALITE.diplome_id = DIPLOME.diplome_id',
				'filters' => [
					[
						'name' => 'specialite_spe1',
						'label' => 'Spécialité 1',
						'column' => 'SPECIALITE.specialite_spe1',
						'type' => 'select',
						'options' => [
							'SVT' => 'SVT',
							'Physique-Chimie' => 'Physique-Chimie',
							'Mathématiques' => 'Mathématiques',
							'NSI' => 'NSI',
							'SES' => 'SES',
							'HGGSP' => 'HGGSP',
							'Philosophie' => 'Philosophie'
						]
					],
					[
						'name' => 'specialite_spe2',
						'label' => 'Spécialité 2',
						'column' => 'SPECIALITE.specialite_spe2',
						'type' => 'select',
						'options' => [
							'SVT' => 'SVT',
							'Physique-Chimie' => 'Physique-Chimie',
							'Mathématiques' => 'Mathématiques',
							'NSI' => 'NSI',
							'SES' => 'SES',
							'HGGSP' => 'HGGSP',
							'Philosophie' => 'Philosophie'
						]
					]
				]
			],

			// Section Établissement — Lycée et localisation
			'etablissement' => [
				'label' => 'Établissement',
				'join' => 'LEFT JOIN ETABLISSEMENT ON ETABLISSEMENT.etablissement_id = CANDIDAT.etablissement_id 
				           LEFT JOIN LOCALISATION ON LOCALISATION.localisation_id = ETABLISSEMENT.localisation_id',
				'filters' => [
					[
						'name' => 'departement',
						'label' => 'Département',
						'column' => 'LOCALISATION.localisation_departement',
						'type' => 'select',
						'options' => [
							'01' => 'Ain (01)',
							'69' => 'Rhône (69)',
							'75' => 'Paris (75)',
							'92' => 'Hauts-de-Seine (92)',
							'13' => 'Bouches-du-Rhône (13)'
						]
					]
				]
			]
		]
	],

	// ============================================================
	// PAGE GROUPES — Filtres sur les groupes et critères
	// ============================================================
	'groupes' => [
		'title' => 'Filtres des groupes',
		'sections' => [

			// Section Groupe — Informations groupe
			'groupe' => [
				'label' => 'Groupe',
				'join' => null,
				'filters' => [
					[
						'name' => 'groupe_nom',
						'label' => 'Nom du groupe',
						'column' => 'GROUPE.groupe_nom',
						'type' => 'text',
						'placeholder' => 'Filtrer par nom'
					],
					[
						'name' => 'groupe_couleur',
						'label' => 'Couleur',
						'column' => 'GROUPE.groupe_couleur',
						'type' => 'color'
					]
				]
			],

			// Section Critères — Critères associés aux filtres
			'criteres' => [
				'label' => 'Critères appliqués',
				'join' => 'LEFT JOIN FILTRE ON FILTRE.groupe_id = GROUPE.groupe_id 
				           LEFT JOIN CRITERE ON CRITERE.critere_id = FILTRE.critere_id',
				'filters' => [
					[
						'name' => 'critere_libelle',
						'label' => 'Critère',
						'column' => 'CRITERE.critere_libelle',
						'type' => 'text',
						'placeholder' => 'Ex: Note minimale'
					],
					[
						'name' => 'critere_filtre',
						'label' => 'Type de filtre',
						'column' => 'CRITERE.critere_filtre',
						'type' => 'select',
						'options' => [
							'note' => 'Note',
							'diplome' => 'Diplôme',
							'specialite' => 'Spécialité',
							'boursier' => 'Boursier',
							'departement' => 'Département'
						]
					]
				]
			],

			// Section Candidats du groupe — Nombre de candidats
			'candidats' => [
				'label' => 'Candidats du groupe',
				'join' => 'LEFT JOIN CANDIDAT ON CANDIDAT.groupe_id = GROUPE.groupe_id',
				'filters' => [
					[
						'name' => 'candidat_boursier',
						'label' => 'Filtre par boursier',
						'column' => 'CANDIDAT.candidat_boursier_code',
						'type' => 'select',
						'options' => [
							'0' => 'Non boursier',
							'1' => 'Boursier échelon 1',
							'2' => 'Boursier échelon 2'
						]
					],
					[
						'name' => 'note_globale_groupe',
						'label' => 'Note Globale (min)',
						'column' => 'CANDIDAT.candidat_note_globale',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					]
				]
			]
		]
	],

	// ============================================================
	// PAGE FORMATIONS — Filtres sur les formations supérieures
	// ============================================================
	'formations' => [
		'title' => 'Filtres des formations',
		'sections' => [

			// Section Formation — Infos formation
			'formation' => [
				'label' => 'Formation',
				'join' => null,
				'filters' => [
					[
						'name' => 'formation_nom',
						'label' => 'Nom de la formation',
						'column' => 'FORMATION_SUP.formation_nom',
						'type' => 'text',
						'placeholder' => 'Licence, BUT, etc.'
					]
				]
			],

			// Section Candidats — Candidats inscrits en formation
			'candidats' => [
				'label' => 'Candidats inscrits',
				'join' => 'LEFT JOIN CANDIDAT ON CANDIDAT.formation_id = FORMATION_SUP.formation_id',
				'filters' => [
					[
						'name' => 'candidat_nom',
						'label' => 'Nom candidat',
						'column' => 'CANDIDAT.candidat_nom',
						'type' => 'text',
						'placeholder' => 'Nom ou prénom'
					],
					[
						'name' => 'candidat_boursier',
						'label' => 'Boursier',
						'column' => 'CANDIDAT.candidat_boursier_code',
						'type' => 'select',
						'options' => [
							'0' => 'Non boursier',
							'1' => 'Boursier échelon 1',
							'2' => 'Boursier échelon 2'
						]
					]
				]
			],

			// Section Notes — Notes des candidats
			'notes' => [
				'label' => 'Notes des candidats',
				'join' => 'LEFT JOIN CANDIDAT ON CANDIDAT.formation_id = FORMATION_SUP.formation_id',
				'filters' => [
					[
						'name' => 'note_globale_min',
						'label' => 'Note Globale (minimum)',
						'column' => 'CANDIDAT.candidat_note_globale',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					],
					[
						'name' => 'note_lycee_min',
						'label' => 'Note Lycée (minimum)',
						'column' => 'CANDIDAT.candidat_note_lycee',
						'type' => 'number',
						'operator' => '>=',
						'min' => 0,
						'max' => 20
					]
				]
			],

			// Section Diplôme — Type de bac des candidats
			'diplome' => [
				'label' => 'Diplômes / Bacs',
				'join' => 'LEFT JOIN CANDIDAT ON CANDIDAT.formation_id = FORMATION_SUP.formation_id 
				           LEFT JOIN DIPLOME ON DIPLOME.diplome_id = CANDIDAT.diplome_id',
				'filters' => [
					[
						'name' => 'type_bac_formation',
						'label' => 'Type de Bac',
						'column' => 'DIPLOME.diplome_type_libelle',
						'type' => 'select',
						'options' => [
							'Général' => 'Général',
							'Technologique' => 'Technologique',
							'Professionnel' => 'Professionnel'
						]
					]
				]
			]
		]
	]

];
