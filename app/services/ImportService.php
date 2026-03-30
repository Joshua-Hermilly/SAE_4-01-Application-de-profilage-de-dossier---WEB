<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once '../app/entities/Candidat.php';
require_once '../app/entities/Etablissement.php';
require_once '../app/entities/Localisation.php';
require_once '../app/entities/Diplome.php';
require_once '../app/entities/FormationSup.php';
require_once '../app/entities/Specialite.php';

require_once '../app/repositories/CandidatRepository.php';
require_once '../app/repositories/DiplomeRepository.php';
require_once '../app/repositories/LocalisationRepository.php';
require_once '../app/repositories/EtablissementRepository.php';
require_once '../app/repositories/FormationSupRepository.php';
require_once '../app/repositories/SpecialiteRepository.php';

class ImportService
{
	/*-------------------------------*/
	/* Repository                    */
	/*-------------------------------*/
	private $localisationRepository;
	private $candidatRepository;
	private $etablissementRepository;
	private $diplomeRepository;
	private $formationsSupRepository;
	private $specialiteRepository;

	/*-------------------------------*/
	/* Entitées                      */
	/*-------------------------------*/
	private array $localisations;
	private array $etablissements;
	private array $diplomes;
	private array $formationsSup;
	private array $specialites;
	private array $candidats;


	/*-------------------------------*/
	/* Construct                     */
	/*-------------------------------*/
	function __construct()
	{
		$this->localisationRepository  = new LocalisationRepository ();
		$this->etablissementRepository = new EtablissementRepository();
		$this->diplomeRepository       = new DiplomeRepository      ();
		$this->formationsSupRepository = new FormationSupRepository ();
		$this->specialiteRepository    = new SpecialiteRepository   ();
		$this->candidatRepository      = new CandidatRepository     ();


		$this->localisations  = $this->localisationRepository ->findAll();
		$this->etablissements = $this->etablissementRepository->findAll();
		$this->diplomes       = $this->diplomeRepository      ->findAll();
		$this->formationsSup  = $this->formationsSupRepository->findAll();
		$this->specialites    = $this->specialiteRepository   ->findAll();
		$this->candidats      = $this->candidatRepository     ->findAll();
	}

	/*-------------------------------*/
	/* Importer                      */
	/*-------------------------------*/
	public function importFile($file)
	{
		$sheet = $file->getActiveSheet();
		$data  = $sheet->toArray(null, true, true);

		$headers = array_shift($data);
		$lignes  = array_map
		(
			fn($ligne) => array_combine($headers, $ligne),
			$data
		);

		$specialiteRelations   = [];
		$candidatRelations     = [];

		foreach ($lignes as $ligne)
		{
			$localisation  = $this->createLoc( $ligne                                         );
			$etablissement = $this->createEtb( $ligne, $localisation                          );
			$diplome       = $this->createDpm( $ligne                                         );
			$formationSup  = $this->createFms( $ligne                                         );
			$specialite    = $this->createSpt( $ligne, $diplome                               );
			$candidat      = $this->createCdt( $ligne,$etablissement, $diplome, $formationSup );

			$specialiteRelations[] = [
				'specialite' => $specialite,
				'diplome'    => $diplome
			];

			$candidatRelations[] = [
				'candidat'      => $candidat,
				'etablissement' => $etablissement,
				'diplome'       => $diplome,
				'formationSup'  => $formationSup
			];
		}

		// Insertion  en base :
		// - - - - - - - - - - -
		$this->localisationRepository ->creates($this->localisations );
		$this->etablissementRepository->creates($this->etablissements);
		$this->formationsSupRepository->creates($this->formationsSup );
		$this->diplomeRepository      ->creates($this->diplomes      );

		for ($cpt = 0; $cpt < count($specialiteRelations); $cpt++)
		{
			$specialite = $specialiteRelations[$cpt]['specialite'];
			$diplome    = $specialiteRelations[$cpt]['diplome'];

			$specialite->setDiplomeId($diplome->getDiplomeId());
		}
		$this->specialiteRepository->creates($this->specialites);

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
	}


	/*-------------------------------*/
	/* Create                        */
	/*-------------------------------*/
	private function createLoc( $ligne ): ?Localisation
	{
		$localisation = new Localisation
		(
			0,
			trim($ligne['Pays Etablissement origine - Libellé 2024/2025'       ]),
			trim($ligne['Commune Etablissement origine - CodePostal 2024/2025' ]),
			trim($ligne['Commune Etablissement origine - Libellé 2024/2025'    ]),
			trim($ligne['Département Etablissement origine - Libellé 2024/2025']),
		);

		foreach ($this->localisations as $loc)
		{
			if ( $localisation->getLocalisationPays       () === $loc->getLocalisationPays       () &&
				 $localisation->getLocalisationCodePostal () === $loc->getLocalisationCodePostal () &&
				 $localisation->getLocalisationCommune    () === $loc->getLocalisationCommune    () &&
				 $localisation->getLocalisationDepartement() === $loc->getLocalisationDepartement()    )
			{
				return $loc;
			}

		}

		//$this->localisationRepository->create($localisation);
		$this->localisations[] = $localisation;
		return $localisation;
	}

	private function createEtb( $ligne, $localisation ): ?Etablissement
	{
		$etablissement = new Etablissement
		(
			0,
			trim($ligne['Nom Etablissement origine 2024/2025']),
			$localisation,
		);

		foreach ($this->etablissements as $etab)
		{
			if ( $etablissement->getEtablissementNom()                      === $etab->getEtablissementNom()                     &&
				 $etablissement->getLocalisation    ()->getLocalisationId() === $etab->getLocalisation    ()->getLocalisationId()  )
			{
				return $etab;
			}
		}

		//$this->etablissementRepository->create($etablissement);
		$this->etablissements[] = $etablissement;
		return $etablissement;
	}

	private function createDpm( $ligne ): ?Diplome
	{
		$diplome = new Diplome
		(
			0,
			trim($ligne['Type Diplôme - Code'    ]),
			trim($ligne['Type Diplôme - Libellé' ]),
			trim($ligne['Série Diplôme - Code'   ]),
			trim($ligne['Série Diplôme - Libellé'])
		);

		foreach ($this->diplomes as $dip)
		{
			if ( $dip->getDiplomeTypeCode    () === $diplome->getDiplomeTypeCode    () &&
				 $dip->getDiplomeSerieCode   () === $diplome->getDiplomeSerieCode   () &&
				 $dip->getDiplomeTypeLibelle () === $diplome->getDiplomeTypeLibelle () &&
				 $dip->getDiplomeSerieLibelle() === $diplome->getDiplomeSerieLibelle()    )
			{
				return $dip;
			}
		}

		//$this->diplomeRepository->create($diplome);
		$this->diplomes[] = $diplome;
		return $diplome;
	}

	private function createFms( $ligne ): ?FormationSup
	{
		$f1 = $ligne ['Filiere (pour scolarité du supérieur)- Libellé 2024/2025' ];
		$f2 = $ligne ['Formation - Libellé (Saisie manuelle) 2024/2025'          ];

		if ( $f1 === $f2 ) { return null;    }
		if ( isset($f1)  ) { $filiere = $f1; }
		else               { $filiere = $f2; }

		$formationSup = new FormationSup
		(
			0,
			$filiere,
		);

		foreach ($this->formationsSup as $fms)
		{
			if ( $fms->getFormationNom() === $formationSup->getFormationNom() )
			{
				return $fms;
			}
		}

		//$this->formationsSupRepository->create($formationSup);
		$this->formationsSup[] = $formationSup;
		return $formationSup;
	}

	private function createSpt( $ligne, $diplome )
	{
		$combinaison = trim((string)($ligne['Combinaison des enseignements de spécialité en Terminale'] ?? ''));
		$parties     = explode('/', $combinaison, 2);
		$spe1        = isset($parties[0]) && $parties[0] !== '' ? trim($parties[0]) : null;
		$spe2        = isset($parties[1]) && $parties[1] !== '' ? trim($parties[1]) : null;

		$specialite = new Specialite
		(
			0,
			trim($ligne['Spécialité - Libellé'                             ]),
			$ligne['Spécialité / Mention - Libellé  2024/2025'        ],
			$spe1,
			$spe2,
			$ligne['Enseignement De spécialité abandonné en Première' ],
			$diplome->getDiplomeId()
		);

		foreach ($this->specialites as $spe)
		{
			if ($spe->getSpecialiteOpt1  () === $specialite->getSpecialiteOpt1  () &&
				$spe->getSpecialiteOpt2  () === $specialite->getSpecialiteOpt2  () &&
				$spe->getSpecialiteSpe1  () === $specialite->getSpecialiteSpe1  () &&
				$spe->getSpecialiteSpe2  () === $specialite->getSpecialiteSpe2  () &&
				$spe->getSpecialiteSpeAbd() === $specialite->getSpecialiteSpeAbd() &&
				$spe->getDiplomeId       () === $specialite->getDiplomeId       ()   )
			{
				return $spe;
			}
		}

		//$this->specialiteRepository->create($specialite);
		$this->specialites[] = $specialite;
		return $specialite;
	}

	private function createCdt( $ligne, $etablissement, $diplome, $formationSup )
	{
		$candidat = new Candidat
		(
			trim(                $ligne['Candidat - Code'          ]),
			trim(                $ligne['Candidat - Nom'           ]),
			trim(                $ligne['Candidat - Prénom'        ]),
			trim(                $ligne['Civilité'                 ]),
			trim(                $ligne['Profil Candidat - Libellé']),
			trim(                $ligne['Candidat boursier - Code' ]),
			$this->getNoteOrNull($ligne['Note Globale Calculée'    ]),
			$this->getNoteOrNull($ligne['Note Fiche Avenir'        ]),
			$this->getNoteOrNull($ligne['Note Lycée calculée'      ]),
			$ligne['Commentaire'              ],
			$etablissement?->getEtablissementId(),
			null,
			$formationSup ?->getFormationId    (),
			$diplome       ->getDiplomeId      (),

		);

		foreach ( $this->candidats as $cdt)
		{
			if ( $candidat->getCandidatCode() === $cdt->getCandidatCode() )
			{
				return $cdt;
			}
		}

		//$this->candidatRepository->create($candidat);
		$this->candidats[] = $candidat;
		return $candidat;
	}

	/*-------------------------------*/
	/* Fonctions privées             */
	/*-------------------------------*/
	private function getNoteOrNull($value)
	{
		$raw = trim((string)($value ?? ''));

		if (preg_match('/^\d/', $raw) === 1)
		{
			return (float) $raw; //str_replace(',', '.', $raw);
		}

		return null;
	}
}