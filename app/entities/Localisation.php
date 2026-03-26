<?php

namespace entities;

class Localisation
{
	private ?int    $localisation_id;
	private ?string $localisation_pays;
	private ?string $localisation_code_postal;
	private ?string $localisation_commune;
	private ?string $localisation_departement;


	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/

	public function __construct(?int $localisation_id, ?string $localisation_pays, ?string $localisation_code_postal, ?string $localisation_commune, ?string $localisation_departement)
	{
		$this->localisation_id          = $localisation_id;
		$this->localisation_pays        = $localisation_pays;
		$this->localisation_code_postal = $localisation_code_postal;
		$this->localisation_commune     = $localisation_commune;
		$this->localisation_departement = $localisation_departement;
	}

	/*---------------------------------*/
	/*       Getters / Setters         */
	/*---------------------------------*/

	public function getLocalisationId(): ?int
	{
		return $this->localisation_id;
	}
	public function setLocalisationId(?int $localisation_id): self
	{
		$this->localisation_id = $localisation_id;
		return $this;
	}

	public function getLocalisationPays(): ?string
	{
		return $this->localisation_pays;
	}
	public function setLocalisationPays(?string $localisation_pays): self
	{
		$this->localisation_pays = $localisation_pays;
		return $this;
	}

	public function getLocalisationCodePostal(): ?string
	{
		return $this->localisation_code_postal;
	}
	public function setLocalisationCodePostal(?string $localisation_code_postal): self
	{
		$this->localisation_code_postal = $localisation_code_postal;
		return $this;
	}

	public function getLocalisationCommune(): ?string
	{
		return $this->localisation_commune;
	}
	public function setLocalisationCommune(?string $localisation_commune): self
	{
		$this->localisation_commune = $localisation_commune;
		return $this;
	}

	public function getLocalisationDepartement(): ?string
	{
		return $this->localisation_departement;
	}
	public function setLocalisationDepartement(?string $localisation_departement): self
	{
		$this->localisation_departement = $localisation_departement;
		return $this;
	}
}
