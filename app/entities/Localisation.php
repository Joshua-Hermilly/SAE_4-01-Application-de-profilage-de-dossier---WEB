<?php

class Localisation
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	public function __construct
	(
		private int     $localisation_id,
		private ?string $localisation_pays,
		private ?string $localisation_code_postal,
		private ?string $localisation_commune,
		private ?string $localisation_departement,
		private ?float  $localisation_latitude,
		private ?float  $localisation_longitude,
		private ?float  $localisation_distance
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

	public function getLocalisationLatitude(): ?float
	{
		return $this->localisation_latitude;
	}

	public function getLocalisationLongitude(): ?float
	{
		return $this->localisation_longitude;
	}

	public function getLocalisationDistance(): ?float
	{
		return $this->localisation_distance;
	}

	/*---------------------------------*/
	/*            Setters              */
	/*---------------------------------*/
	public function setLocalisationId($localisation_id): void
	{
		$this->localisation_id = $localisation_id;
	}

	public function setLocalisationPays($localisation_pays): void
	{
		$this->localisation_pays = $localisation_pays;
	}

	public function setLocalisationCodePostal($localisation_code_postal): void
	{
		$this->localisation_code_postal = $localisation_code_postal;
	}

	public function setLocalisationCommune($localisation_commune): void
	{
		$this->localisation_commune = $localisation_commune;
	}

	public function setLocalisationDepartement($localisation_departement): void
	{
		$this->localisation_departement = $localisation_departement;
	}

	public function setLocalisationLatitude($localisation_latitude): void
	{
		$this->localisation_latitude = $localisation_latitude;
	}

	public function setLocalisationLongitude($localisation_longitude): void
	{
		$this->localisation_longitude = $localisation_longitude;
	}

	public function setLocalisationDistance($localisation_distance): void
	{
		$this->localisation_distance = $localisation_distance;
	}
}
