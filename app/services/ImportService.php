<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once '../app/services/RechercheLocalisationService.php';

require_once '../app/entities/Candidat.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Diplome.php';
require_once '../app/entities/FormationSup.php';
require_once '../app/entities/Specialite.php';

require_once '../app/repositories/CandidatRepository.php';
require_once '../app/repositories/DiplomeRepository.php';
require_once '../app/repositories/EtablissementRepository.php';
require_once '../app/repositories/FormationSupRepository.php';
require_once '../app/repositories/SpecialiteRepository.php';

class ImportService
{
	/*-------------------------------*/
	/* Colonnes du tableurs          */
	/*-------------------------------*/
	private const CLEES_COLONNES = 
	[
		// clées                // numéro de colonne
		'candidat_code'      =>                  0  ,  // Candidat - Code
		'candidat_nom'       =>                  1  ,  // Candidat - Nom
		'candidat_prenom'    =>                  2  ,  // Candidat - Prénom
		'civilite'           =>                  3  ,  // Civilité
		'profil'             =>                  4  ,  // Profil Candidat - Libellé
		'boursier'           =>                  5  ,  // Candidat boursier - Code
		'filiere'            =>                  6  ,  // Filiere (pour scolarité du supérieur)- Libellé 2024/2025
		'formation'          =>                  7  ,  // Formation - Libellé (Saisie manuelle) 2024/2025
		'spe_mention'        =>                  8  ,  // Spécialité / Mention - Libellé  2024/2025
		'etab_nom'           =>                  9  ,  // Nom Etablissement origine 2024/2025
		'commune_libelle'    =>                 10  ,  // Commune Etablissement origine - Libellé 2024/2025
		'commune_cp'         =>                 11  ,  // Commune Etablissement origine - CodePostal 2024/2025
		'departement'        =>                 12  ,  // Département Etablissement origine - Libellé 2024/2025
		'pays'               =>                 13  ,  // Pays Etablissement origine - Libellé 2024/2025
		'diplome_type_code'  =>                 14  ,  // Type Diplôme - Code
		'diplome_type_lib'   =>                 15  ,  // Type Diplôme - Libellé
		'diplome_serie_code' =>                 16  ,  // Série Diplôme - Code
		'diplome_serie_lib'  =>                 17  ,  // Série Diplôme - Libellé
		'spe_libelle'        =>                 18  ,  // Spécialité - Libellé
		'spe_combinaison'    =>                 19  ,  // Combinaison des enseignements de spécialité en Terminale
		'spe_abandonnee'     =>                 20  ,  // Enseignement De spécialité abandonné en Première
		'note_globale'       =>                 21  ,  // Note Globale Calculée
		'note_avenir'        =>                 22  ,  // Note Fiche Avenir
		'note_lycee'         =>                 23  ,  // Note Lycée calculée
		'note_dossier'       =>                 24  ,  // Note Dossier
		'commentaire'        =>                 25  ,  // Commentaire
	];

	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $candidatRepository;
	private $etablissementRepository;
	private $diplomeRepository;
	private $formationsSupRepository;
	private $specialiteRepository;

	/*-------------------------------*/
	/* Entitées                      */
	/*-------------------------------*/
	private array $etablissements;
	private array $diplomes;
	private array $formationsSup;
	private array $specialites;
	private array $candidats;

	/*-------------------------------*/
	/* Service  localisation         */
	/*-------------------------------*/
	private $serviceRechercheLocalisation;

	/*-------------------------------*/
	/* Construct                     */
	/*-------------------------------*/
	function __construct()
	{
		$this->etablissementRepository      = new EtablissementRepository            ();
		$this->diplomeRepository            = new DiplomeRepository                  ();
		$this->formationsSupRepository      = new FormationSupRepository             ();
		$this->specialiteRepository         = new SpecialiteRepository               ();
		$this->candidatRepository           = new CandidatRepository                 ();

		$this->etablissements               = $this->etablissementRepository->findAll();
		$this->diplomes                     = $this->diplomeRepository      ->findAll();
		$this->formationsSup                = $this->formationsSupRepository->findAll();
		$this->specialites                  = $this->specialiteRepository   ->findAll();
		$this->candidats                    = $this->candidatRepository     ->findAll();

		$this->serviceRechercheLocalisation = new RechercheLocalisationService       ();
	}

	/*-------------------------------*/
	/* Importer                      */
	/*-------------------------------*/
	public function importFile($file, $annee)
	{
		$sheet = $file->getActiveSheet();
		$data  = $sheet->toArray(null, true, true);
		array_shift($data); 

		$candidatRelations   = [];

		foreach ($data as $ligne)
		{
			if (count($ligne) < count(self::CLEES_COLONNES)) { continue; } //skip les lignes problematiques

			$etablissement = $this->createEtb( $ligne                                                  );
			$specialite    = $this->createSpt( $ligne                                                  );
			$diplome       = $this->createDpm( $ligne, $specialite                                     );
			$formationSup  = $this->createFms( $ligne                                                  );
			$candidat      = $this->createCdt( $ligne, $etablissement, $diplome, $formationSup, $annee );

			$candidatRelations[] = [
				'candidat'      => $candidat,
				'etablissement' => $etablissement,
				'diplome'       => $diplome,
				'formationSup'  => $formationSup
			];
		}

		// Insertion en base
		$this->etablissementRepository->creates($this->etablissements);
		$this->formationsSupRepository->creates($this->formationsSup );
		$this->specialiteRepository   ->creates($this->specialites   );
		$this->diplomeRepository      ->creates($this->diplomes      );

		for ($cpt = 0; $cpt < count($candidatRelations); $cpt++)
		{
			$candidat      = $candidatRelations[$cpt]['candidat'     ];
			$etablissement = $candidatRelations[$cpt]['etablissement'];
			$diplome       = $candidatRelations[$cpt]['diplome'      ];
			$formationSup  = $candidatRelations[$cpt]['formationSup' ];

			$candidat->setEtablissementId($etablissement?->getEtablissementId());
			$candidat->setFormationId    ($formationSup ?->getFormationId    ());
			$candidat->setDiplomeId      ($diplome       ->getDiplomeId      ());
		}
		$this->candidatRepository->creates($this->candidats);


		$fichier = $this->serviceRechercheLocalisation->remplissageTerminee();
		$retour  = $this->serviceRechercheLocalisation->appelerApi($fichier);
		if ( $retour ) { $this->serviceRechercheLocalisation->parcoursTableau($retour); }

		if (session_status() === PHP_SESSION_NONE) { session_start(); }
		$_SESSION['data_version'] = (string)microtime(true);
		unset($_SESSION['filter_cache']);

	}


	/*-------------------------------*/
	/* Create                        */
	/*-------------------------------*/
	private function createEtb(array $ligne)
	{
		$etablissement = new Etablissement
		(
			0,
			$this->getCol($ligne, 'etab_nom'       ),
			$this->getCol($ligne, 'pays'           ),
			$this->getCol($ligne, 'commune_cp') == '99999' ? null : $this->getCol($ligne, 'commune_cp'),
			$this->getCol($ligne, 'commune_libelle'),
			$this->getCol($ligne, 'departement'    ),
			null,
			null,
			null
		);

		foreach ($this->etablissements as $etab)
		{
			if ( $etablissement->getEtablissementNom        () === $etab->getEtablissementNom        () &&
				 $etablissement->getEtablissementPays       () === $etab->getEtablissementPays       () &&
			     $etablissement->getEtablissementCommune    () === $etab->getEtablissementCommune    () &&
			     $etablissement->getEtablissementCodePostal () === $etab->getEtablissementCodePostal () &&
			     $etablissement->getEtablissementDepartement() === $etab->getEtablissementDepartement()    )
			{
				return $etab;
			}
		}

		$this->etablissements[] = $etablissement;
		$this->serviceRechercheLocalisation->addEtablissement($etablissement);
		return $etablissement;
	}

	private function createDpm(array $ligne, $specialite)
	{
		$diplome = new Diplome
		(
			0,
			$this->getCol($ligne, 'diplome_type_code' ),
			$this->getCol($ligne, 'diplome_type_lib'  ),
			$this->getCol($ligne, 'diplome_serie_code'),
			$this->getCol($ligne, 'diplome_serie_lib' ),
			$specialite,
			null,
		);

		foreach ($this->diplomes as $dip)
		{
			if ( $dip->getDiplomeTypeCode    () === $diplome->getDiplomeTypeCode    () &&
				 $dip->getDiplomeSerieCode   () === $diplome->getDiplomeSerieCode   () &&
				 $dip->getDiplomeTypeLibelle () === $diplome->getDiplomeTypeLibelle () &&
				 $dip->getDiplomeSerieLibelle() === $diplome->getDiplomeSerieLibelle() &&
			     $dip->getSpecialite         () === $diplome->getSpecialite         ()    )
			{
				return $dip;
			}
		}

		$this->diplomes[] = $diplome;
		return $diplome;
	}

	private function createFms(array $ligne): ?FormationSup
	{
		$f1 = (string)($this->getCol($ligne, 'filiere'  ) ?? '');
		$f2 = (string)($this->getCol($ligne, 'formation') ?? '');

		$filiere = $f1 !== '' ? $f1 : ($f2 !== '' ? $f2 : null);
		if ($filiere === null) { return null; }

		$formationSup = new FormationSup
		(
			0,
			$this->getCol($ligne, 'filiere'  ),
			$this->getCol($ligne, 'formation')
		);

		foreach ($this->formationsSup as $fms)
		{
			if ( $fms->getFormationNom() === $formationSup->getFormationNom() &&
			     $fms->getFormationLib() === $formationSup->getFormationLib()   )
			{
				return $fms;
			}
		}

		$this->formationsSup[] = $formationSup;
		return $formationSup;
	}

	private function createSpt(array $ligne): ?Specialite
	{
		$combinaison = (string)($this->getCol($ligne, 'spe_combinaison') ?? '');
		$parties     = explode('/', $combinaison);

		$spe1 = trim(($parties[0] ?? '')) ?: null;
		$spe2 = trim(($parties[1] ?? '')) ?: null;
		$spe3 = trim(($parties[3] ?? '')) ?: null;

		$specialite = new Specialite
		(
			0,
			$this->getCol($ligne, 'spe_libelle'   ),
			$this->getCol($ligne, 'spe_mention'   ),
			$spe1,
			$spe2,
			$spe3,
			$this->getCol($ligne, 'spe_abandonnee')
		);

		foreach ($this->specialites as $spe)
		{
			if ($spe->getSpecialiteOpt1  () === $specialite->getSpecialiteOpt1  () &&
				$spe->getSpecialiteOpt2  () === $specialite->getSpecialiteOpt2  () &&
				$spe->getSpecialiteSpe1  () === $specialite->getSpecialiteSpe1  () &&
				$spe->getSpecialiteSpe2  () === $specialite->getSpecialiteSpe2  () &&
				$spe->getSpecialiteSpe3  () === $specialite->getSpecialiteSpe3  () &&
				$spe->getSpecialiteSpeAbd() === $specialite->getSpecialiteSpeAbd()   )
			{
				return $spe;
			}
		}

		$this->specialites[] = $specialite;
		return $specialite;
	}

	private function createCdt(array $ligne, $etablissement, $diplome, $formationSup, $annee)
	{
		$candidat = new Candidat
		(
			$this->getCol($ligne, 'candidat_code'  ),
			$this->getCol($ligne, 'candidat_nom'   ),
			$this->getCol($ligne, 'candidat_prenom'),
			$this->getCol($ligne, 'civilite'       ),
			$this->getCol($ligne, 'profil'         ),
			$this->getCol($ligne, 'boursier'       ),
			$this->getNoteOrNull($this->getCol($ligne, 'note_globale')),
			$this->getNoteOrNull($this->getCol($ligne, 'note_avenir' )),
			$this->getNoteOrNull($this->getCol($ligne, 'note_lycee'  )),
			$this->getCol($ligne, 'commentaire'),
			$annee,
			$etablissement?->getEtablissementId(),
			null,
			$formationSup ?->getFormationId    (),
			$diplome      ?->getDiplomeId      (),
		);

		foreach ($this->candidats as $cdt)
		{
			if ( $candidat->getCandidatCode() === $cdt->getCandidatCode() )
			{
				return $cdt;
			}
		}

		$this->candidats[] = $candidat;
		return $candidat;
	}

	/*-------------------------------*/
	/* Fonctions privées             */
	/*-------------------------------*/
	private function getCol(array $ligne, string $key)
	{
		$val = $ligne[self::CLEES_COLONNES[$key]] ?? null;

		if ( isset($val) && trim($val) !== '' ) { return trim($val); }
		return null;
	}

	private function getNoteOrNull($value)
	{
		$raw = (string)($value ?? '');

		if (preg_match('/^\d/', $raw) === 1)	{ return (float) $raw;}
		return null;
	}
}