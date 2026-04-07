<?php

class Etablissement implements JsonSerializable
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	public function __construct
	(
		private int           $etablissement_id,
		private ?string       $etablissement_nom,
		private ?string       $etablissement_pays,
		private ?string       $etablissement_code_postal,
		private ?string       $etablissement_commune,
		private ?string       $etablissement_departement,
		private ?float        $etablissement_latitude,
		private ?float        $etablissement_longitude,
		private ?float        $etablissement_distance,

		private ?array        $candidats  = []
	) {}

	public function jsonSerialize(): array
	{
		return
		[
			'etablissement_id'          => $this->etablissement_id,
			'etablissement_nom'         => $this->etablissement_nom,
			'etablissement_pays'        => $this->etablissement_pays,
			'etablissement_code_postal' => $this->etablissement_code_postal,
			'etablissement_commune'     => $this->etablissement_commune,
			'etablissement_departement' => $this->etablissement_departement,
			'etablissement_latitude'    => $this->etablissement_latitude,
			'etablissement_longitude'   => $this->etablissement_longitude,
			'etablissement_distance'    => $this->etablissement_distance,
			'nb_candidats'              => count( $this->candidats )
		];
	}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getEtablissementId(): int
	{
		return $this->etablissement_id;
	}

	public function getEtablissementNom(): ?string
	{
		return $this->etablissement_nom;
	}

	public function getEtablissementPays(): ?string
	{
		return $this->etablissement_pays;
	}

	public function getEtablissementCodePostal(): ?string
	{
		return $this->etablissement_code_postal;
	}

	public function getEtablissementCommune(): ?string
	{
		return $this->etablissement_commune;
	}

	public function getEtablissementDepartement(): ?string
	{
		return $this->etablissement_departement;
	}

	public function getEtablissementLatitude(): ?float
	{
		return $this->etablissement_latitude;
	}

	public function getEtablissementLongitude(): ?float
	{
		return $this->etablissement_longitude;
	}

	public function getEtablissementDistance(): ?float
	{
		return $this->etablissement_distance;
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

	public function setEtablissementNom(?string $etablissement_nom): void
	{
		$this->etablissement_nom = $etablissement_nom;
	}

	public function setEtablissementPays(?string $etablissement_pays): void
	{
		$this->etablissement_pays = $etablissement_pays;
	}

	public function setEtablissementCodePostal(?string $etablissement_code_postal): void
	{
		$this->etablissement_code_postal = $etablissement_code_postal;
	}

	public function setEtablissementCommune(?string $etablissement_commune): void
	{
		$this->etablissement_commune = $etablissement_commune;
	}

	public function setEtablissementDepartement(?string $etablissement_departement): void
	{
		$this->etablissement_departement = $etablissement_departement;
	}

	public function setEtablissementLatitude(?float $etablissement_latitude): void
	{
		$this->etablissement_latitude = $etablissement_latitude;
	}

	public function setEtablissementLongitude(?float $etablissement_longitude): void
	{
		$this->etablissement_longitude = $etablissement_longitude;
	}

	public function setEtablissementDistance(?float $etablissement_distance): void
	{
		$this->etablissement_distance = $etablissement_distance;
	}

	public function setCandidats(?array $candidats): void
	{
		$this->candidats = $candidats;
	}
}