<?php
require_once '../app/core/Repository.php';
require_once '../app/entities/DossierCandidat.php';

class DossierCandidatRepository
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
	public function createDossierCandidatFromRow(array $row): DossierCandidat
	{
		return new DossierCandidat
		(
			$row['candidat_code'],
			$row['candidat_civilite'],
			$row['candidat_boursier_code'],
			round($row['candidat_note_lycee'  ] ?? -1, 2),
			round($row['candidat_note_fiche'  ] ?? -1, 2),
			round($row['candidat_note_globale'] ?? -1, 2),
			$row['etablissement_nom'],
			$row['diplome_serie_code'],
			$row['specialite_spe1'],
			$row['specialite_spe2'],
			$row['groupe_couleur'],
		);
	}

	public function findAll(): array
	{
		$sql = "SELECT
					C.candidat_code,
					C.candidat_civilite,
					C.candidat_boursier_code,
					C.candidat_note_lycee,
					C.candidat_note_fiche,
					C.candidat_note_globale,
					E.etablissement_nom,
					D.diplome_serie_code,
					S.specialite_spe1,
					S.specialite_spe2,
					G.groupe_couleur
				FROM 
					CANDIDAT AS C
						LEFT JOIN ETABLISSEMENT AS E ON E.etablissement_id = C.etablissement_id
						LEFT JOIN DIPLOME AS D ON D.diplome_id = C.diplome_id
						LEFT JOIN SPECIALITE AS S ON S.specialite_id = D.specialite_id
						LEFT JOIN GROUPE AS G ON G.groupe_id = C.groupe_id
				ORDER BY C.candidat_code";

		$stmt = $this->pdo->query($sql);

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createDossierCandidatFromRow($row);
		}
		return $result;
	}

	public function findByPage(int $page): array
	{
		$sql = "SELECT
					C.candidat_code     , C.candidat_civilite, C.candidat_boursier_code, C.candidat_note_lycee, C.candidat_note_fiche, C.candidat_note_globale,
					E.etablissement_nom ,
					D.diplome_serie_code,
					S.specialite_spe1   , S.specialite_spe2,
					G.groupe_couleur
				FROM 
					CANDIDAT AS C
						LEFT JOIN ETABLISSEMENT AS E ON E.etablissement_id = C.etablissement_id
						LEFT JOIN DIPLOME       AS D ON D.diplome_id       = C.diplome_id
						LEFT JOIN SPECIALITE    AS S ON S.specialite_id    = D.specialite_id
						LEFT JOIN GROUPE        AS G ON G.groupe_id        = C.groupe_id
				ORDER BY C.candidat_code
				OFFSET :page ROWS
				LIMIT 25";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':page', (int)(($page - 1) * 25));
		$stmt->execute();

		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = $this->createDossierCandidatFromRow($row);
		}
		return $result;
	}


	public function nbMaxDossier(): int
	{
		$sql = "SELECT COUNT(*) FROM CANDIDAT";
		$stmt = $this->pdo->prepare($sql);

		$stmt->execute();
		return $stmt->fetchColumn();
	}
}
