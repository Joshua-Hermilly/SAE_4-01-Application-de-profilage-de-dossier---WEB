<?php

require_once '../app/core/Repository.php';

class FormationStatsRepository
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

	public function getLatestYear(): ?int
	{
		$sql  = 'SELECT MAX(candidat_annee) AS annee FROM CANDIDAT';
		$stmt = $this->pdo->query($sql);
		$row  = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
		return $row && $row['annee'] !== null ? (int) $row['annee'] : null;
	}

	private function buildBaseQuery(?int $annee, bool $count = false): array
	{
		$params = [];

		if ($count)
		{
			$sql = "SELECT COUNT(*) FROM (
				SELECT 1
				FROM   CANDIDAT   AS C
				LEFT JOIN DIPLOME    D ON D.diplome_id    = C.diplome_id
				LEFT JOIN SPECIALITE S ON S.specialite_id  = D.specialite_id";

			if ($annee !== null)
			{
				$sql              .= "\nWHERE C.candidat_annee = :annee";
				$params[':annee'] = $annee;
			}

			$sql .= "\nGROUP BY
					D.diplome_serie_libelle,
					CONCAT_WS(' / ',
						NULLIF(TRIM(COALESCE(S.specialite_spe1, '')), ''),
						NULLIF(TRIM(COALESCE(S.specialite_spe2, '')), ''),
						NULLIF(TRIM(COALESCE(S.specialite_spe3, '')), ''))
				) AS sub";
		}
		else
		{
			$sql = "SELECT
				D.diplome_serie_libelle AS serie_bac,
				CONCAT_WS(' / ',
					NULLIF(TRIM(COALESCE(S.specialite_spe1, '')), ''),
					NULLIF(TRIM(COALESCE(S.specialite_spe2, '')), ''),
					NULLIF(TRIM(COALESCE(S.specialite_spe3, '')), '')) AS combinaison_spe,
				COUNT(*)                                                            AS total_voeux,
				SUM(CASE WHEN C.candidat_civilite = 'Mme' THEN 1 ELSE 0 END)        AS filles,
				SUM(CASE WHEN C.candidat_civilite = 'M.'  THEN 1 ELSE 0 END)        AS garcons,
				SUM(CASE WHEN C.candidat_boursier_code <> 0 THEN 1 ELSE 0 END)      AS boursiers,
				SUM(CASE WHEN C.candidat_boursier_code  = 0 THEN 1 ELSE 0 END)      AS non_boursiers
			FROM   CANDIDAT   AS C
			LEFT JOIN DIPLOME    D ON D.diplome_id    = C.diplome_id
			LEFT JOIN SPECIALITE S ON S.specialite_id  = D.specialite_id";

			if ($annee !== null)
			{
				$sql              .= "\nWHERE C.candidat_annee = :annee";
				$params[':annee'] = $annee;
			}

			$sql .= "\nGROUP BY
				D.diplome_serie_libelle,
				CONCAT_WS(' / ',
					NULLIF(TRIM(COALESCE(S.specialite_spe1, '')), ''),
					NULLIF(TRIM(COALESCE(S.specialite_spe2, '')), ''),
					NULLIF(TRIM(COALESCE(S.specialite_spe3, '')), ''))
			ORDER BY D.diplome_serie_libelle, combinaison_spe";
		}

		return [$sql, $params];
	}

	public function countRows(?int $annee): int
	{
		[$sql, $params] = $this->buildBaseQuery($annee, true);
		$stmt           = $this->pdo->prepare($sql);

		foreach ($params as $k => $v)
		{
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		}

		$stmt->execute();
		return (int) $stmt->fetchColumn();
	}

	public function findByPage(int $page, ?int $annee, int $limit = 25): array
	{
		[$baseSql, $params] = $this->buildBaseQuery($annee, false);
		$offset             = max(0, ($page - 1) * $limit);
		$sql                = $baseSql . "\nLIMIT :limit\nOFFSET :offset";

		$stmt = $this->pdo->prepare($sql);
		foreach ($params as $k => $v)
		{
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		}
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();

		$rows = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$rows[] = $row;
		}

		return $rows;
	}
}
