<?php

class FormationSup
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private  int    $formation_id,
		private ?string $formation_nom,
		private ?string $formation_lib,
	) {}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getFormationId(): int
	{
		return $this->formation_id;
	}

	public function getFormationNom(): ?string
	{
		return $this->formation_nom;
	}

	public function getFormationLib(): ?string
	{
		return $this->formation_lib;
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

	public function setFormationLib(string $formation_lib): void
	{
		$this->formation_lib = $formation_lib;
	}
}