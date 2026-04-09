<?php

class FormationStat implements JsonSerializable
{
	/*-------------------------------*/
	/*  Constructeur                 */
	/*-------------------------------*/
	public function __construct
	(
		private ?string $serie_bac,
		private ?string $combinaison_spe,
		private int     $total_voeux,
		private int     $filles,
		private int     $garcons,
		private int     $boursiers,
		private int     $non_boursiers,
	){}

	/*-------------------------------*/
	/*  Header                       */
	/*-------------------------------*/
	public function getHeader(): array
	{
		return [
			'Série de bac réformé',
			'Combinaison des enseignements de spécialités de Terminale',
			"Total des vœux de l'année N",
			'Filles',
			'Garçons',
			'Boursiers certifiés des lycées',
			'Non Boursiers certifiés des lycées',
		];
	}

	/*-------------------------------*/
	/*  Serialize                    */
	/*-------------------------------*/
	public function jsonSerialize(): mixed { return $this->__serialize(); }

	public function __serialize(): array
	{
		return [
			'Série de bac réformé'                                     => $this->serie_bac       ?? 'Non défini',
			'Combinaison des enseignements de spécialités de Terminale' => $this->combinaison_spe ?? '',
			"Total des vœux de l'année N"                             => $this->total_voeux,
			'Filles'                                                    => $this->filles,
			'Garçons'                                                   => $this->garcons,
			'Boursiers certifiés des lycées'                            => $this->boursiers,
			'Non Boursiers certifiés des lycées'                        => $this->non_boursiers
		];
	}
}
