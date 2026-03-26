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
		
		private ?Etablissement $etablissement,
		private ?Groupe        $groupe,
		private Diplome        $diplome
    ) {}

	/*-------------------------------*/
	/* Getters                       */
	/*-------------------------------*/
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

	public function getCandidatCommentaire(): ?string
	{
		return $this->candidat_commentaire;
	}
    
	public function getEtablissement(): ?Etablissement
	{
		return $this->etablissement;
	}

	public function getGroupe(): ?Groupe
	{
		return $this->groupe;
	}

	public function getDiplome(): Diplome
	{
		return $this->diplome;
	}
	
	/*-------------------------------*/
	/* Setters                       */
	/*-------------------------------*/
	public function setCandidatCommentaire(?string $candidat_commentaire): void
	{
		$this->candidat_commentaire = $candidat_commentaire;
	}

	public function setCandidatNoteGlobal(?float $candidat_note_global): void
	{
		$this->candidat_note_global = $candidat_note_global;
	}

	public function setCandidatNoteFiche(?float $candidat_note_fiche): void
	{
		$this->candidat_note_fiche = $candidat_note_fiche;
	}

	public function setCandidatNoteLycee(?float $candidat_note_lycee): void
	{
		$this->candidat_note_lycee = $candidat_note_lycee;
	}

	public function setCandidatBoursierCode(int $candidat_boursier_code): void
	{
		if ( $candidat_boursier_code < 0 || $candidat_boursier_code > 2 ) { return; }

		$this->candidat_boursier_code = $candidat_boursier_code;
	}

	public function setCandidatProfil(string $candidat_profil): void
	{
		$this->candidat_profil = $candidat_profil;
	}

	public function setCandidatCivilite(string $candidat_civilite): void
	{
		if ( $candidat_civilite !== 'M.' && $candidat_civilite !== 'Mme') { return; }

		$this->candidat_civilite = $candidat_civilite;
	}

	public function setCandidatPrenom(string $candidat_prenom): void
	{
		$this->candidat_prenom = $candidat_prenom;
	}

	public function setCandidatNom(string $candidat_nom): void
	{
		$this->candidat_nom = $candidat_nom;
	}

	public function setCandidatCode(int $candidat_code): void
	{
		$this->candidat_code = $candidat_code;
	}
	
	public function setEtablissement(Etablissement $etablissement): void
	{
		$this->etablissement = $etablissement;
	}

	public function setGroupe(?Groupe $groupe): void
	{
		$this->groupe = $groupe;
	}

	public function setDiplome(Diplome $diplome): void
	{
		$this->diplome = $diplome;
	}
}