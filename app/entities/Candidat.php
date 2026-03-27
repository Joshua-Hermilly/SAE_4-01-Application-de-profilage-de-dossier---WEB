<?php

class Candidat
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private int            $candidat_code,
		private string         $candidat_nom,
		private string         $candidat_prenom,
		private string         $candidat_civilite,
		private string         $candidat_profil,
		private int            $candidat_boursier_code,
		private ?float         $candidat_note_lycee,
		private ?float         $candidat_note_fiche,
		private ?float         $candidat_note_global,
		private ?string        $candidat_commentaire,

		// Relation exterieur
		private ?int  $etablissement_id,
		private ?int  $groupe_id,
		private ?int  $formation_id,
		private int   $diplome_id
	) {}

	/*-------------------------------*/
	/* Getters                       */
	/*-------------------------------*/
	public function getCandidatCommentaire(): ?string
	{
		return $this->candidat_commentaire;
	}

	public function getCandidatCode(): int
	{
		return $this->candidat_code;
	}

	public function getCandidatNom(): string
	{
		return $this->candidat_nom;
	}

	public function getCandidatPrenom(): string
	{
		return $this->candidat_prenom;
	}

	public function getCandidatCivilite(): string
	{
		return $this->candidat_civilite;
	}

	public function getCandidatProfil(): string
	{
		return $this->candidat_profil;
	}

	public function getCandidatBoursierCode(): int
	{
		return $this->candidat_boursier_code;
	}

	public function getCandidatNoteLycee(): ?float
	{
		return $this->candidat_note_lycee;
	}

	public function getCandidatNoteFiche(): ?float
	{
		return $this->candidat_note_fiche;
	}

	public function getCandidatNoteGlobal(): ?float
	{
		return $this->candidat_note_global;
	}

	public function getEtablissementId(): ?int
	{
		return $this->etablissement_id;
	}

	public function getGroupeId(): ?int
	{
		return $this->groupe_id;
	}

	public function getFormationId(): ?int
	{
		return $this->formation_id;
	}

	public function getDiplomeId(): int
	{
		return $this->diplome_id;
	}

	/*-------------------------------*/
	/* Setters                       */
	/*-------------------------------*/
	public function setCandidatCode(int $candidat_code): void
	{
		$this->candidat_code = $candidat_code;
	}

	public function setCandidatNom(string $candidat_nom): void
	{
		$this->candidat_nom = $candidat_nom;
	}

	public function setCandidatPrenom(string $candidat_prenom): void
	{
		$this->candidat_prenom = $candidat_prenom;
	}

	public function setCandidatCivilite(string $candidat_civilite): void
	{
		$this->candidat_civilite = $candidat_civilite;
	}

	public function setCandidatProfil(string $candidat_profil): void
	{
		$this->candidat_profil = $candidat_profil;
	}

	public function setCandidatBoursierCode(int $candidat_boursier_code): void
	{
		$this->candidat_boursier_code = $candidat_boursier_code;
	}

	public function setCandidatNoteLycee(?float $candidat_note_lycee): void
	{
		$this->candidat_note_lycee = $candidat_note_lycee;
	}

	public function setCandidatNoteFiche(?float $candidat_note_fiche): void
	{
		$this->candidat_note_fiche = $candidat_note_fiche;
	}

	public function setCandidatNoteGlobal(?float $candidat_note_global): void
	{
		$this->candidat_note_global = $candidat_note_global;
	}

	public function setCandidatCommentaire(?string $candidat_commentaire): void
	{
		$this->candidat_commentaire = $candidat_commentaire;
	}

	public function setEtablissementId(?int $etablissement_id): void
	{
		$this->etablissement_id = $etablissement_id;
	}

	public function setGroupeId(?int $groupe_id): void
	{
		$this->groupe_id = $groupe_id;
	}

	public function setFormationId(?int $formation_id): void
	{
		$this->formation_id = $formation_id;
	}

	public function setDiplomeId(int $diplome_id): void
	{
		$this->diplome_id = $diplome_id;
	}
}