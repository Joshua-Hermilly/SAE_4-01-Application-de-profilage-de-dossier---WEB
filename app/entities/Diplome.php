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
		private string $diplome_statut,

		// Relation
		private array $candidats  = [],
		private array $formations = []
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

	public function getDiplomeStatut(): string
	{
		return $this->diplome_statut;
	}

	public function getCandidats(): array
	{
		return $this->candidats;
	}

	public function getFormations(): array
	{
		return $this->formations;
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

	public function setDiplomeStatut(string $diplome_statut): void
	{
		$this->diplome_statut = $diplome_statut;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}

	public function setFormations(array $formations): void
	{
		$this->formations = $formations;
	}
}
