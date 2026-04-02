<?php

class Diplome
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	function __construct
	(
		private int    $diplome_id,
		private int    $diplome_type_code,
		private string $diplome_type_libelle,
		private string $diplome_serie_code,
		private string $diplome_serie_libelle,

		// Relation
		private Specialite $specialite,
		private ?array     $candidats  = [],
	) {}

	/*---------------------------------*/
	/*             Getters             */
	/*---------------------------------*/
	public function getDiplomeId(): int
	{
		return $this->diplome_id;
	}

	public function getDiplomeTypeCode(): int
	{
		return $this->diplome_type_code;
	}

	public function getDiplomeTypeLibelle(): string
	{
		return $this->diplome_type_libelle;
	}

	public function getDiplomeSerieCode(): string
	{
		return $this->diplome_serie_code;
	}

	public function getDiplomeSerieLibelle(): string
	{
		return $this->diplome_serie_libelle;
	}
	public function getCandidats(): ?	array
	{
		return $this->candidats;
	}

	public function getSpecialite(): Specialite
	{
		return $this->specialite;
	}

	/*---------------------------------*/
	/*             Setters             */
	/*---------------------------------*/
	public function setDiplomeId(int $diplome_id): void
	{
		$this->diplome_id = $diplome_id;
	}

	public function setDiplomeTypeCode(int $diplome_type_code): void
	{
		$this->diplome_type_code = $diplome_type_code;
	}

	public function setDiplomeTypeLibelle(string $diplome_type_libelle): void
	{
		$this->diplome_type_libelle = $diplome_type_libelle;
	}

	public function setDiplomeSerieCode(string $diplome_serie_code): void
	{
		$this->diplome_serie_code = $diplome_serie_code;
	}

	public function setDiplomeSerieLibelle(string $diplome_serie_libelle): void
	{
		$this->diplome_serie_libelle = $diplome_serie_libelle;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}

	public function setSpecialite(Specialite $specialite): void
	{
		$this->specialite = $specialite;
	}
}
