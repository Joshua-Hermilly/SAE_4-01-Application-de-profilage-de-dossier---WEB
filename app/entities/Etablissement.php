<?php

class Etablissement
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct
	(
		private int           $etablissement_id,
		private ?string       $etablissement_nom,

		private Localisation  $localisation,
		private ?array        $candidats  = []
	) {}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getEtablissementId(): ?int
	{
		return $this->etablissement_id;
	}

	public function getEtablissementNom(): ?string
	{
		return $this->etablissement_nom;
	}

	public function getLocalisation(): Localisation
	{
		return $this->localisation;
	}

	public function getCandidats(): ?array
	{
		return $this->candidats;
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setEtablissementId(int $etablissement_id): void
	{
		$this->etablissement_id = $etablissement_id;
	}

	public function setEtablissementNom(string $etablissement_nom): void
	{
		$this->etablissement_nom = $etablissement_nom;
	}

	public function setLocalisation(Localisation $localisation): void
	{
		$this->localisation = $localisation;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}
}