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
}