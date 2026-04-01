<?php

require_once '../app/core/Repository.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
	/*-------------------------------*/
	/* Colonnes du tableur           */
	/*-------------------------------*/
	private const HEADERS = [
		'Code Candidat',
		'Nom Candidat',
		'Prénom',
		'Civilité',
		'Profil Candidat - Libellé',
		'Candidat boursier - Code',
		'Filiere (pour scolarité du supérieur)- Libellé PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Formation - Libellé (Saisie manuelle) PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Spécialité / Mention - Libellé PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Nom Etablissement origine PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Commune Etablissement origine - Libellé PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Commune Etablissement origine - CodePostal PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Département Etablissement origine - Libellé PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Pays Etablissement origine - Libellé PLACEHOLDER_ANNEE-1/PLACEHOLDER_ANNEE',
		'Type Diplôme - Code',
		'Type Diplôme - Libellé',
		'Série Diplôme - Code',
		'Série Diplôme - Libellé',
		'Spécialité - Libellé',
		'Combinaison des enseignements de spécialité en Terminale',
		'Enseignement De spécialité abandonné en Première',
		'Note Globale Calculée',
		'Note Fiche Avenir',
		'Note Lycée calculée',
		'Note Dossier',
		'Commentaire',
	];

	private $pdo;

	public function __construct()
	{
		$this->pdo = Repository::getInstance()->getPDO();
	}

	public function exportXLSX(int $annee): void
	{
		$headers = $this->construireHeaders($annee);
		$donnees = $this->fetchDonnees($annee);

		$spreadsheet = new Spreadsheet();
		$feuille = $spreadsheet->getActiveSheet();
		$feuille->setTitle('Export');

		// Ligne d'en-têtes
		$feuille->fromArray($headers, null, 'A1');

		// Données, à partir de la ligne 2
		$feuille->fromArray($donnees, null, 'A2');

		$writer = new Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Total Promotion ' . ($annee - 1) . '-' . $annee . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit();
	}

	private function construireHeaders(int $annee): array
	{
		$anneePrec = $annee - 1;
		return array_map(
			function (string $header) use ($annee, $anneePrec): string
			{
				return str_replace(
					['PLACEHOLDER_ANNEE-1', 'PLACEHOLDER_ANNEE'],
					[(string)$anneePrec, (string)$annee],
					$header
				);
			},
			self::HEADERS
		);
	}

	/*-------------------------------*/
	/*  Récupération des données     */
	/*-------------------------------*/
	private function fetchDonnees($annee): array
	{
		$sql = "SELECT
			C.candidat_code,
			C.candidat_nom,
			C.candidat_prenom,
			C.candidat_civilite,
			C.candidat_profil,
			C.candidat_boursier_code,
			FS.formation_nom         AS formation_filiere,
			FS.formation_nom         AS formation_libelle,
			string_agg(DISTINCT S.specialite_opt2, ' / ')               AS specialite_mention,
			E.etablissement_nom,
			L.localisation_commune,
			L.localisation_code_postal,
			L.localisation_departement,
			L.localisation_pays,
			D.diplome_type_code,
			D.diplome_type_libelle,
			D.diplome_serie_code,
			D.diplome_serie_libelle,
			string_agg(DISTINCT S.specialite_opt1, ' / ')               AS specialite_libelle,
			string_agg(DISTINCT NULLIF(S.specialite_spe1,  ''), ' / ')  AS specialite_spe1,
			string_agg(DISTINCT NULLIF(S.specialite_spe2,  ''), ' / ')  AS specialite_spe2,
			string_agg(DISTINCT NULLIF(S.specialite_spe3,  ''), ' / ')  AS specialite_spe3,
			string_agg(DISTINCT NULLIF(S.specialite_speabd,''), ' / ')  AS specialite_speabd,
			C.candidat_note_globale,
			C.candidat_note_fiche,
			C.candidat_note_lycee,
			G.groupe_note_dossier,
			C.candidat_commentaire
		FROM
			          CANDIDAT      C
			LEFT JOIN FORMATION_SUP FS ON FS.formation_id = C.formation_id
			LEFT JOIN ETABLISSEMENT E  ON E.etablissement_id = C.etablissement_id
			LEFT JOIN LOCALISATION  L  ON L.localisation_id = E.localisation_id
			LEFT JOIN DIPLOME       D  ON D.diplome_id       = C.diplome_id
			LEFT JOIN SPECIALITE    S  ON S.diplome_id       = D.diplome_id
			LEFT JOIN GROUPE        G  ON G.groupe_id        = C.groupe_id
		WHERE
			C.candidat_annee = :annee
		GROUP BY
			C.candidat_code,
			C.candidat_nom,
			C.candidat_prenom,
			C.candidat_civilite,
			C.candidat_profil,
			C.candidat_boursier_code,
			FS.formation_nom,
			E.etablissement_nom,
			L.localisation_commune,
			L.localisation_code_postal,
			L.localisation_departement,
			L.localisation_pays,
			D.diplome_type_code,
			D.diplome_type_libelle,
			D.diplome_serie_code,
			D.diplome_serie_libelle,
			C.candidat_note_globale,
			C.candidat_note_fiche,
			C.candidat_note_lycee,
			G.groupe_note_dossier,
			C.candidat_commentaire
		ORDER BY C.candidat_code";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':annee', $annee, PDO::PARAM_INT);
		$stmt->execute();
		$result = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $result[] = $this->mapRow($row); }

		return $result;
	}

	private function mapRow(array $row): array
	{
		$combinaisonSpe = $this->creerCombinaisonSpecialite(
			$row['specialite_spe1'] ?? null,
			$row['specialite_spe2'] ?? null,
			$row['specialite_spe3'] ?? null,
		);

		return [
			$row['candidat_code'],
			$row['candidat_nom'],
			$row['candidat_prenom'],
			$row['candidat_civilite'],
			$row['candidat_profil'],
			$row['candidat_boursier_code'],
			$row['formation_filiere'],
			$row['formation_libelle'],
			$row['specialite_mention'],
			$row['etablissement_nom'],
			$row['localisation_commune'],
			$row['localisation_code_postal'],
			$row['localisation_departement'],
			$row['localisation_pays'],
			$row['diplome_type_code'],
			$row['diplome_type_libelle'],
			$row['diplome_serie_code'],
			$row['diplome_serie_libelle'],
			$row['specialite_libelle'],
			$combinaisonSpe,
			$row['specialite_speabd'],
			$row['candidat_note_globale'] !== null ? round((float)$row['candidat_note_globale'], 2) : null,
			$row['candidat_note_fiche']   !== null ? round((float)$row['candidat_note_fiche'], 2)   : null,
			$row['candidat_note_lycee']   !== null ? round((float)$row['candidat_note_lycee'], 2)   : null,
			$row['groupe_note_dossier']   !== null ? round((float)$row['groupe_note_dossier'], 2)   : null,
			$row['candidat_commentaire'],
		];
	}

	private function creerCombinaisonSpecialite(?string $spe1, ?string $spe2, ?string $spe3): ?string
	{
		$specialitesBase = [];
		foreach ([$spe1, $spe2, $spe3] as $valeur)
		{
			if ($valeur !== null && trim($valeur) !== '') { $specialitesBase[] = $valeur; }
		}

		if (empty($specialitesBase)) { return null; }

		$specialiteFini = [];
		foreach ($specialitesBase as $texte)
		{
			$morceaux = explode('/', $texte);
			foreach ($morceaux as $mot)
			{
				$mot = trim($mot);
				if ($mot !== '' && !in_array($mot, $specialiteFini, true)) { $specialiteFini[] = $mot; }
			}
		}

		return empty($specialiteFini) ? null : implode(' / ', $specialiteFini);
	}
}