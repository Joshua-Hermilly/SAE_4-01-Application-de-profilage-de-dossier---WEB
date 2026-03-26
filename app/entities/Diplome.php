<?php

namespace entities;

class Diplome
{
	private ?int    $diplome_id;
	private ?int    $diplome_type_code;
	private ?string $diplome_type_libelle;
	private ?string $diplome_serie_code;
	private ?string $diplome_serie_libelle;

	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/

	public function __construct(?int $diplome_id, ?int $diplome_type_code, ?string $diplome_type_libelle, ?string $diplome_serie_code, ?string $diplome_serie_libelle)
	{
		$this->diplome_id            = $diplome_id;
		$this->diplome_type_code     = $diplome_type_code;
		$this->diplome_type_libelle  = $diplome_type_libelle;
		$this->diplome_serie_code    = $diplome_serie_code;
		$this->diplome_serie_libelle = $diplome_serie_libelle;
	}

	/*---------------------------------*/
	/*       Getters / Setters         */
	/*---------------------------------*/

	public function getDiplomeId(): ?int
	{
		return $this->diplome_id;
	}
	public function setDiplomeId(?int $diplome_id): self
	{
		$this->diplome_id = $diplome_id;
		return $this;
	}

	public function getDiplomeTypeCode(): ?int
	{
		return $this->diplome_type_code;
	}
	public function setDiplomeTypeCode(?int $diplome_type_code): self
	{
		$this->diplome_type_code = $diplome_type_code;
		return $this;
	}

	public function getDiplomeTypeLibelle(): ?string
	{
		return $this->diplome_type_libelle;
	}
	public function setDiplomeTypeLibelle(?string $diplome_type_libelle): self
	{
		$this->diplome_type_libelle = $diplome_type_libelle;
		return $this;
	}

	public function getDiplomeSerieCode(): ?string
	{
		return $this->diplome_serie_code;
	}
	public function setDiplomeSerieCode(?string $diplome_serie_code): self
	{
		$this->diplome_serie_code = $diplome_serie_code;
		return $this;
	}

	public function getDiplomeSerieLibelle(): ?string
	{
		return $this->diplome_serie_libelle;
	}
	public function setDiplomeSerieLibelle(?string $diplome_serie_libelle): self
	{
		$this->diplome_serie_libelle = $diplome_serie_libelle;
		return $this;
	}
}
