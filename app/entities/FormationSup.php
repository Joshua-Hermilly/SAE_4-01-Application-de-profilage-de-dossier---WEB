<?php

class FormationSup
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private int    $formation_id,
		private string $formation_nom,

		private array  $specialites    = [],
		private array  $etablissements = [],
		private array  $diplomes       = [],
	) {}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getFormationId(): int
	{
		return $this->formation_id;
	}

	public function getFormationNom(): string
	{
		return $this->formation_nom;
	}

	public function getSpecialites(): array
	{
		return $this->specialites;
	}

	public function getEtablissements(): array
	{
		return $this->etablissements;
	}

	public function getDiplomes(): array
	{
		return $this->diplomes;
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setFormationId(int $formation_id): void
	{
		$this->formation_id = $formation_id;
	}

	public function setFormationNom(string $formation_nom): void
	{
		$this->formation_nom = $formation_nom;
	}

		public function setSpecialites(array $specialites): void
	{
		$this->specialites = $specialites;
	}

	public function setEtablissements(array $etablissements): void
	{
		$this->etablissements = $etablissements;
	}

	public function setDiplomes(array $diplomes): void
	{
		$this->diplomes = $diplomes;
	}
}