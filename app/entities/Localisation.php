<?php

class Localisation
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	public function __construct(
		private ?int    $localisation_id,
		private ?string $localisation_pays,
		private ?string $localisation_code_postal,
		private ?string $localisation_commune,
		private ?string $localisation_departement
	) {}

	/*---------------------------------*/
	/*            Getters              */
	/*---------------------------------*/
	public function getLocalisationId(): ?int
	{
		return $this->localisation_id;
	}

	public function getLocalisationPays(): ?string
	{
		return $this->localisation_pays;
	}

	public function getLocalisationCodePostal(): ?string
	{
		return $this->localisation_code_postal;
	}

	public function getLocalisationCommune(): ?string
	{
		return $this->localisation_commune;
	}

	public function getLocalisationDepartement(): ?string
	{
		return $this->localisation_departement;
	}

	/*---------------------------------*/
	/*            Setters              */
	/*---------------------------------*/
	public function setLocalisationId($localisation_id): self
	{
		$this->localisation_id = $localisation_id;
		return $this;
	}

	public function setLocalisationPays($localisation_pays): self
	{
		$this->localisation_pays = $localisation_pays;
		return $this;
	}

	public function setLocalisationCodePostal($localisation_code_postal): self
	{
		$this->localisation_code_postal = $localisation_code_postal;
		return $this;
	}

	public function setLocalisationCommune($localisation_commune): self
	{
		$this->localisation_commune = $localisation_commune;
		return $this;
	}

	public function setLocalisationDepartement($localisation_departement): self
	{
		$this->localisation_departement = $localisation_departement;
		return $this;
	}
}
