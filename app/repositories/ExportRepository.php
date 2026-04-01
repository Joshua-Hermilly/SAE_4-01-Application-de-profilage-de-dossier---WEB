<?php

require_once '../app/core/Repository.php';

require_once '../app/entities/Candidat.php';
require_once '../app/entities/Diplome.php';
require_once '../app/entities/Export.php';
require_once '../app/entities/FormationSup.php';
require_once '../app/entities/Groupe.php';
require_once '../app/entities/Localisation.php';
require_once '../app/entities/Specialite.php';


class ExportRepository extends Repository
{
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
	private function createFromRow($row): Export
	{
		return new Export
		(
			$row['candidat_code'           ],
			$row['candidat_nom'            ],
			$row['candidat_prenom'         ],
			$row['candidat_civilite'       ],
			$row['candidat_profil'         ],
			$row['candidat_boursier_code'  ],
			$row['formation_filiere'       ],
			$row['formation_libelle'       ],
			$row['specialite_mention'      ],
			$row['etablissement_nom'       ],
			$row['localisation_commune'    ],
			$row['localisation_code_postal'],
			$row['localisation_departement'],
			$row['localisation_pays'       ],
			$row['diplome_type_code'       ],
			$row['diplome_type_libelle'    ],
			$row['diplome_serie_code'      ],
			$row['diplome_serie_libelle'   ],
			$row['specialite_opt1'         ],
			$row['specialite_spe1'         ],
			$row['specialite_spe2'         ],
			$row['specialite_spe3'         ],
			$row['specialite_speabd'       ],
			$row['candidat_note_globale'   ],
			$row['candidat_note_fiche'     ],
			$row['candidat_note_lycee'     ],
			$row['groupe_note_dossier'     ],
			$row['candidat_commentaire'    ]
		);
	}

	public function findAll( $annee): array
	{
		$sql = "SELECT
					C.candidat_code         , 
					C.candidat_nom          , 
					C.candidat_prenom       , 
					C.candidat_civilite     , 
					C.candidat_profil       , 
					C.candidat_boursier_code, 
					F.formation_filliere    AS formation_filiere, 
					F.formation_libelle     AS formation_libelle,
					E.etablissement_nom       ,
					L.localisation_commune    ,
					L.localisation_code_postal,
					L.localisation_departement,
					L.localisation_pays       ,
					D.diplome_type_code     ,
					D.diplome_type_libelle  ,
					D.diplome_serie_code    ,
					D.diplome_serie_libelle ,
					string_agg(DISTINCT NULLIF(S.specialite_opt1,  ''), ' / ')  AS specialite_libelle,
					string_agg(DISTINCT NULLIF(S.specialite_spe1,  ''), ' / ')  AS specialite_spe1   ,
					string_agg(DISTINCT NULLIF(S.specialite_spe2,  ''), ' / ')  AS specialite_spe2   ,
					string_agg(DISTINCT NULLIF(S.specialite_spe3,  ''), ' / ')  AS specialite_spe3   ,
					string_agg(DISTINCT NULLIF(S.specialite_speabd,''), ' / ')  AS specialite_speabd ,
					C.candidat_note_globale ,				
					C.candidat_note_fiche   ,
					C.candidat_note_lycee   ,
					G.groupe_note_dossier   ,
					C.candidat_commentaire  ,
					
				FROM
					CANDIDAT C
					LEFT JOIN FORMATION_SUP F ON F.formation_id     = C.formation_id
					LEFT JOIN ETABLISSEMENT E ON E.etablissement_id = C.etablissement_id
					LEFT JOIN LOCALISATION  L ON L.localisation_id  = E.localisation_id
					LEFT JOIN DIPLOME       D ON D.diplome_id       = C.diplome_id
					LEFT JOIN SPECIALITE    S ON S.specialite_id    = D.specialite_id
					LEFT JOIN GROUPE        G ON G.groupe_id        = C.groupe_id
					
				WHERE 
				    C.candidat_annee = :annee
				    
				GROUP BY
				    C.candidat_code            ,
					C.candidat_nom             ,
					C.candidat_prenom          ,
					C.candidat_civilite        ,
					C.candidat_profil          ,
					C.candidat_boursier_code   ,
					F.formation_nom            ,
					E.etablissement_nom        ,
					L.localisation_commune     ,
					L.localisation_code_postal ,
					L.localisation_departement ,
					L.localisation_pays        ,
					D.diplome_type_code        ,
					D.diplome_type_libelle     ,
					D.diplome_serie_code       ,
					D.diplome_serie_libelle    ,
					C.candidat_note_globale    ,
					C.candidat_note_fiche      ,
					C.candidat_note_lycee      ,
					G.groupe_note_dossier      ,
					C.candidat_commentaire
				
				ORDER BY C.candidat_code";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':annee', $annee );
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createFromRow($row);
		}

		return $result;
	}
}