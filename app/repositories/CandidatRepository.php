 <?PHP

require_once '../app/core/Repository.php';
require_once '../app/entities/Candidat.php';

class CandidatRepository
{
	/*-------------------------------*/
	/*          Attributs            */
	/*-------------------------------*/
	private $pdo;

	/*-------------------------------*/
	/*         Constructeur          */
	/*-------------------------------*/
	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	/*-------------------------------*/
	/*            Méthodes           */
	/*-------------------------------*/
	public function create(Candidat $candidat)
	{
		$sql = "INSERT INTO CANDIDAT 
				(candidat_code, candidat_nom, candidat_prenom, candidat_civilite, candidat_profil, candidat_boursier_code,
				 candidat_note_lycee, candidat_note_fiche, candidat_note_globale, candidat_commentaire, candidat_annee,
				 etablissement_id, groupe_id, formation_id, diplome_id)
				VALUES 
				(:code, :nom, :prenom, :civilite, :profil, :boursier, 
				 :note_lycee, :note_fiche, :note_globale, :commentaire, :annne,
				 :etablissement, :groupe, :formation, :diplome)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':code'         , $candidat->getCandidatCode        ());
		$stmt->bindValue(':nom'          , $candidat->getCandidatNom         ());
		$stmt->bindValue(':prenom'       , $candidat->getCandidatPrenom      ());
		$stmt->bindValue(':civilite'     , $candidat->getCandidatCivilite    ());
		$stmt->bindValue(':profil'       , $candidat->getCandidatProfil      ());
		$stmt->bindValue(':boursier'     , $candidat->getCandidatBoursierCode());
		$stmt->bindValue(':note_lycee'   , $candidat->getCandidatNoteLycee   ());
		$stmt->bindValue(':note_fiche'   , $candidat->getCandidatNoteFiche   ());
		$stmt->bindValue(':note_globale' , $candidat->getCandidatNoteGlobal  ());
		$stmt->bindValue(':commentaire'  , $candidat->getCandidatCommentaire ());
		$stmt->bindValue(':annee'        , $candidat->getCandidatAnnee       ());
		$stmt->bindValue(':diplome'      , $candidat->getDiplomeId           ());
		$stmt->bindValue(':etablissement', $candidat->getEtablissementId     ());
		$stmt->bindValue(':groupe'       , $candidat->getGroupeId            ());
		$stmt->bindValue(':formation'    , $candidat->getFormationId         ());

		$stmt->execute();
	}

	public function creates(array $candidats)
	{
		$valeurBrut = [];
		$valeurBind = [];

		for ($cpt = 0; $cpt < count($candidats); $cpt++)
		{
			$candidat     = $candidats[$cpt];
			$valeurBind[] = "(:code{$cpt}, :nom{$cpt}, :prenom{$cpt}, :civilite{$cpt}, :profil{$cpt}, :boursier{$cpt}, :note_lycee{$cpt}, :note_fiche{$cpt}, :note_globale{$cpt}, :commentaire{$cpt}, :annee{$cpt}, :etablissement{$cpt},:groupe{$cpt}, :formation{$cpt}, :diplome{$cpt})";

			$valeurBrut[":code{$cpt}"         ] = $candidat->getCandidatCode        ();
			$valeurBrut[":nom{$cpt}"          ] = $candidat->getCandidatNom         ();
			$valeurBrut[":prenom{$cpt}"       ] = $candidat->getCandidatPrenom      ();
			$valeurBrut[":civilite{$cpt}"     ] = $candidat->getCandidatCivilite    ();
			$valeurBrut[":profil{$cpt}"       ] = $candidat->getCandidatProfil      ();
			$valeurBrut[":boursier{$cpt}"     ] = $candidat->getCandidatBoursierCode();
			$valeurBrut[":note_lycee{$cpt}"   ] = $candidat->getCandidatNoteLycee   ();
			$valeurBrut[":note_fiche{$cpt}"   ] = $candidat->getCandidatNoteFiche   ();
			$valeurBrut[":note_globale{$cpt}" ] = $candidat->getCandidatNoteGlobal  ();
			$valeurBrut[":commentaire{$cpt}"  ] = $candidat->getCandidatCommentaire ();
			$valeurBrut[":annee{$cpt}"        ] = $candidat->getCandidatAnnee       ();
			$valeurBrut[":etablissement{$cpt}"] = $candidat->getEtablissementId     ();
			$valeurBrut[":groupe{$cpt}"       ] = $candidat->getGroupeId            ();
			$valeurBrut[":formation{$cpt}"    ] = $candidat->getFormationId         ();
			$valeurBrut[":diplome{$cpt}"      ] = $candidat->getDiplomeId           ();
		}

		$sql = "INSERT INTO CANDIDAT
				(candidat_code, candidat_nom, candidat_prenom, candidat_civilite, candidat_profil, candidat_boursier_code,
				 candidat_note_lycee, candidat_note_fiche, candidat_note_globale, candidat_commentaire, candidat_annee,
				 etablissement_id, groupe_id, formation_id, diplome_id)
				VALUES " . implode(', ', $valeurBind);

		$stmt = $this->pdo->prepare($sql);

		for ($cpt = 0; $cpt < count($candidats); $cpt++)
		{
			$stmt->bindValue(":code{$cpt}"         , $valeurBrut[":code{$cpt}"         ]);
			$stmt->bindValue(":nom{$cpt}"          , $valeurBrut[":nom{$cpt}"          ]);
			$stmt->bindValue(":prenom{$cpt}"       , $valeurBrut[":prenom{$cpt}"       ]);
			$stmt->bindValue(":civilite{$cpt}"     , $valeurBrut[":civilite{$cpt}"     ]);
			$stmt->bindValue(":profil{$cpt}"       , $valeurBrut[":profil{$cpt}"       ]);
			$stmt->bindValue(":boursier{$cpt}"     , $valeurBrut[":boursier{$cpt}"     ]);
			$stmt->bindValue(":note_lycee{$cpt}"   , $valeurBrut[":note_lycee{$cpt}"   ]);
			$stmt->bindValue(":note_fiche{$cpt}"   , $valeurBrut[":note_fiche{$cpt}"   ]);
			$stmt->bindValue(":note_globale{$cpt}" , $valeurBrut[":note_globale{$cpt}" ]);
			$stmt->bindValue(":commentaire{$cpt}"  , $valeurBrut[":commentaire{$cpt}"  ]);
			$stmt->bindValue(":annee{$cpt}"        , $valeurBrut[":annee{$cpt}"        ]);
			$stmt->bindValue(":etablissement{$cpt}", $valeurBrut[":etablissement{$cpt}"]);
			$stmt->bindValue(":groupe{$cpt}"       , $valeurBrut[":groupe{$cpt}"       ]);
			$stmt->bindValue(":formation{$cpt}"    , $valeurBrut[":formation{$cpt}"    ]);
			$stmt->bindValue(":diplome{$cpt}"      , $valeurBrut[":diplome{$cpt}"      ]);
		}

		$stmt->execute();
	}

	public function update(Candidat $candidat): bool
	{
		$sql = "UPDATE CANDIDAT SET
				candidat_nom           = :nom,
				candidat_prenom        = :prenom,
				candidat_civilite      = :civilite,
				candidat_profil        = :profil,
				candidat_boursier_code = :boursier,
				candidat_note_lycee    = :note_lycee,
				candidat_note_fiche    = :note_fiche,
				candidat_note_globale  = :note_globale,
				candidat_annee         = :annee,
				candidat_commentaire   = :commentaire,
				etablissement_id       = :etablissement,
				groupe_id              = :groupe,
				diplome_id             = :diplome
				WHERE candidat_code = :code";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':code'         , $candidat->getCandidatCode        ());
		$stmt->bindValue(':nom'          , $candidat->getCandidatNom         ());
		$stmt->bindValue(':prenom'       , $candidat->getCandidatPrenom      ());
		$stmt->bindValue(':civilite'     , $candidat->getCandidatCivilite    ());
		$stmt->bindValue(':profil'       , $candidat->getCandidatProfil      ());
		$stmt->bindValue(':boursier'     , $candidat->getCandidatBoursierCode());
		$stmt->bindValue(':note_lycee'   , $candidat->getCandidatNoteLycee   ());
		$stmt->bindValue(':note_fiche'   , $candidat->getCandidatNoteFiche   ());
		$stmt->bindValue(':note_globale' , $candidat->getCandidatNoteGlobal  ());
		$stmt->bindValue(':commentaire'  , $candidat->getCandidatCommentaire ());
		$stmt->bindValue(':annee'        , $candidat->getCandidatAnnee       ());
		$stmt->bindValue(':diplome'      , $candidat->getDiplomeId           ());
		$stmt->bindValue(':etablissement', $candidat->getEtablissementId     ());
		$stmt->bindValue(':groupe'       , $candidat->getGroupeId            ());
		$stmt->bindValue(':formation'    , $candidat->getFormationId         ());

		return $stmt->execute();
	}

	public function createCandidatFromRow(array $row): Candidat
	{
		return new Candidat
		(
			(int)$row['candidat_code'],
			$row['candidat_nom'     ],
			$row['candidat_prenom'  ],
			$row['candidat_civilite'],
			$row['candidat_profil'  ],
			(int)$row['candidat_boursier_code'],
			$row['candidat_note_lycee'  ] !== null ? (float)$row['candidat_note_lycee'  ] : null,
			$row['candidat_note_fiche'  ] !== null ? (float)$row['candidat_note_fiche'  ] : null,
			$row['candidat_note_globale'] !== null ? (float)$row['candidat_note_globale'] : null,
			$row['candidat_commentaire'],
			(int)$row['candidat_annee'],
			(int)$row['diplome_id'],
			$row['etablissement_id'] !== null ? (int)$row['etablissement_id'] : null,
			$row['groupe_id'       ] !== null ? (int)$row['groupe_id'       ] : null,
			$row['formation_id'    ] !== null ? (int)$row['formation_id'    ] : null
		);
	}

	public function findById(int $id): ?Candidat
	{
		$sql  = "SELECT * FROM CANDIDAT WHERE candidat_code = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row  = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row) {	return $this->createCandidatFromRow($row);}
		return null;
	}

	public function findAll(): array
	{
		$sql = "SELECT * FROM CANDIDAT";
		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createCandidatFromRow($row);
		}
		return $result;
	}

	public function findByGroupeId(int $groupe_id): array
	{
		$sql = "SELECT * FROM CANDIDAT WHERE groupe_id = :groupe_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':groupe_id', $groupe_id);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createCandidatFromRow($row);
		}
		return $result;
	}

	public function findByFormationId(int $formation_id): array
	{
		$sql = "SELECT * FROM CANDIDAT WHERE formation_id = :formation_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':formation_id', $formation_id);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createCandidatFromRow($row);
		}
		return $result;
	}

	public function findByEtablissementId(int $etablissement_id): array
	{
		$sql = "SELECT * FROM CANDIDAT WHERE etablissement_id = :etablissement_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':etablissement_id', $etablissement_id);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createCandidatFromRow($row);
		}
		return $result;

	}

	public function findByAnneeId(int $annee): array
	{
		$sql = "SELECT * FROM CANDIDAT WHERE candidat_annee = :annee";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':annee', $annee);
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createCandidatFromRow($row);
		}
		return $result;

	}

	public function assignGroupToCodes(int $groupeId, array $codes): void
	{
		if (empty($codes)) { return; }

		$placeholders = [];
		$params       = [':groupe_id' => $groupeId];
		foreach (array_values($codes) as $index => $code)
		{
			$ph = ':code_' . $index;
			$placeholders[]   = $ph;
			$params[$ph] = (int) $code;
		}

		$sql = 'UPDATE CANDIDAT SET groupe_id = :groupe_id WHERE candidat_code IN (' . implode(', ', $placeholders) . ')';
		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $key => $value)
		{
			$stmt->bindValue($key, $value);
		}
		$stmt->execute();
	}
}