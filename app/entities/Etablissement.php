<?php

namespace entities;

class Etablissement
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct
	(
		private int           $etablissemet_id,
		private string        $etablissement_nom,

		private array         $candidats  = [],
		private array         $formations = [],
		private Localisation  $localisation,
	) {}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getEtablissementId(): int
	{
		return $this->etablissemet_id;
	}

	public function getEtablissementNom(): string
	{
		return $this->etablissement_nom;
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
	public function setEtablissementId(int $etablissemet_id): void
	{
		$this->etablissemet_id = $etablissemet_id;
	}

	public function setEtablissementNom(string $etablissement_nom): void
	{
		$this->etablissement_nom = $etablissement_nom;
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