<?php

class Diplome
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	function __construct(
		private ?int    $diplome_id,
		private ?int    $diplome_type_code,
		private ?string $diplome_type_libelle,
		private ?string $diplome_serie_code,
		private ?string $diplome_serie_libelle
	) {}

	/*---------------------------------*/
	/*             Getters             */
	/*---------------------------------*/
	public function getDiplomeId(): ?int
	{
		return $this->diplome_id;
	}

	public function getDiplomeTypeCode(): ?int
	{
		return $this->diplome_type_code;
	}

	public function getDiplomeTypeLibelle(): ?string
	{
		return $this->diplome_type_libelle;
	}

	public function getDiplomeSerieCode(): ?string
	{
		return $this->diplome_serie_code;
	}

	public function getDiplomeSerieLibelle(): ?string
	{
		return $this->diplome_serie_libelle;
	}

	/*---------------------------------*/
	/*             Setters             */
	/*---------------------------------*/
	public function setDiplomeId($diplome_id): self
	{
		$this->diplome_id = $diplome_id;
		return $this;
	}

	public function setDiplomeTypeCode($diplome_type_code): self
	{
		$this->diplome_type_code = $diplome_type_code;
		return $this;
	}

	public function setDiplomeTypeLibelle($diplome_type_libelle): self
	{
		$this->diplome_type_libelle = $diplome_type_libelle;
		return $this;
	}

	public function setDiplomeSerieCode($diplome_serie_code): self
	{
		$this->diplome_serie_code = $diplome_serie_code;
		return $this;
	}

	public function setDiplomeSerieLibelle($diplome_serie_libelle): self
	{
		$this->diplome_serie_libelle = $diplome_serie_libelle;
		return $this;
	}
}
