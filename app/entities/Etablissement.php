<?php

namespace entities;

class Etablissement
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct
	(
		private int           $candidat_id,
		private string        $nom,

		private array         $candidats  = [],
		private array         $formations = [],
		private Localisation  $localisation,
	) {}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getCandidatId(): int
	{
		return $this->candidat_id;
	}

	public function getNom(): string
	{
		return $this->nom;
	}

	public function getCandidats(): array
	{
		return $this->candidats;
	}

	public function getFormations(): array
	{
		return $this->formations;
	}

	public function getLocalisation(): Localisation
	{
		return $this->localisation;
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setCandidatId(int $candidat_id): void
	{
		$this->candidat_id = $candidat_id;
	}

	public function setNom(string $nom): void
	{
		$this->nom = $nom;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}

	public function setFormations(array $formations): void
	{
		$this->formations = $formations;
	}

	public function setLocalisation(Localisation $localisation): void
	{
		$this->localisation = $localisation;
	}
}