<?php

class Groupe
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private int     $groupe_id,
		private ?string $groupe_nom,
		//private ?string $groupe_couleur,

		private array $criteres  = [],
		private array $candidats = []
	){}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getGroupeId(): int
	{
		return $this->groupe_id;
	}

	public function getGroupeNom(): ?string
	{
		return $this->groupe_nom;
	}

	public function getCriteres(): array
	{
		return $this->criteres;
	}

	public function getCandidats(): array
	{
		return $this->candidats;
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setGroupeId(int $groupe_id): void
	{
		$this->groupe_id = $groupe_id;
	}

	public function setGroupeNom(?string $groupe_nom): void
	{
		$this->groupe_nom = $groupe_nom;
	}

	public function setFiltre(array $criteres): void
	{
		$this->criteres = $criteres;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}
}