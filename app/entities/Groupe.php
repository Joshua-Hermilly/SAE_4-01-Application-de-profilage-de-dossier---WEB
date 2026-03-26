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

		private Filtre $filtre,
		private array  $candidats = []
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

	public function getFiltre(): Filtre
	{
		return $this->filtre;
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

	public function setFiltre(Filtre $filtre): void
	{
		$this->filtre = $filtre;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}
}