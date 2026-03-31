<?php

class DossierCandidat implements JsonSerializable
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		// Candidats
		private  string $candidat_code         ,
		private  string $candidat_civilite     ,
		private  string $candidat_boursier_code,
		private ?string $candidat_note_lycee   ,
		private ?string $candidat_note_fiche   ,
		private ?string $candidat_note_globale ,

		// Etablissement
		private ?string $etablissement_nom,

		// Diplome
		private  string $diplome_libelle,

		// Specialite
		private ?string $specicalite_spe1,
		private ?string $specicalite_spe2,

		// Groupe
		private ?string $groupe_couleur
	) {}

	/*-------------------------------*/
	/*  Serialize                    */
	/*-------------------------------*/
	public function jsonSerialize(): mixed { return $this->__serialize(); }

	public function __serialize()
	{
		return
		[
			'candidat_code'          => $this->candidat_code         ,
			'candidat_civilite'      => $this->candidat_civilite     ,
			'candidat_boursier_code' => $this->candidat_boursier_code,
			'candidat_note_lycee'    => $this->candidat_note_lycee   ,
			'candidat_note_fiche'    => $this->candidat_note_fiche   ,
			'candidat_note_globale'  => $this->candidat_note_globale ,
			'etablissement_nom'      => $this->etablissement_nom     ,
			'diplome_libelle'        => $this->diplome_libelle       ,
			'specicalite_spe1'       => $this->specicalite_spe1      ,
			'specicalite_spe2'       => $this->specicalite_spe2      ,
			'groupe_couleur'         => $this->groupe_couleur        ,
		];
	}

	/*-------------------------------*/
	/*  Accesseurs                   */
	/*-------------------------------*/
	public function getGroupeCouleur(): ?string
	{
		return $this->groupe_couleur;
	}

	public function getSpecicaliteSpe2(): string
	{
		return $this->specicalite_spe2;
	}

	public function getSpecicaliteSpe1(): string
	{
		return $this->specicalite_spe1;
	}

	public function getDiplomeLibelle(): string
	{
		return $this->diplome_libelle;
	}

	public function getEtablissementNom(): ?string
	{
		return $this->etablissement_nom;
	}

	public function getCandidatNoteGlobale(): ?string
	{
		return $this->candidat_note_globale;
	}

	public function getCandidatNoteFiche(): ?string
	{
		return $this->candidat_note_fiche;
	}

	public function getCandidatNoteLycee(): ?string
	{
		return $this->candidat_note_lycee;
	}

	public function getCandidatBoursierCode(): string
	{
		return $this->candidat_boursier_code;
	}

	public function getCandidatCivilite(): string
	{
		return $this->candidat_civilite;
	}

	public function getCandidatCode(): string
	{
		return $this->candidat_code;
	}

	/*-------------------------------*/
	/*  Modificateurs                */
	/*-------------------------------*/
	public function setSpecicaliteSpe1(string $specicalite_spe1): void
	{
		$this->specicalite_spe1 = $specicalite_spe1;
	}

	public function setCandidatCode(string $candidat_code): void
	{
		$this->candidat_code = $candidat_code;
	}

	public function setCandidatCivilite(string $candidat_civilite): void
	{
		$this->candidat_civilite = $candidat_civilite;
	}

	public function setCandidatBoursierCode(string $candidat_boursier_code): void
	{
		$this->candidat_boursier_code = $candidat_boursier_code;
	}

	public function setCandidatNoteLycee(?string $candidat_note_lycee): void
	{
		$this->candidat_note_lycee = $candidat_note_lycee;
	}

	public function setCandidatNoteFiche(?string $candidat_note_fiche): void
	{
		$this->candidat_note_fiche = $candidat_note_fiche;
	}

	public function setCandidatNoteGlobale(?string $candidat_note_globale): void
	{
		$this->candidat_note_globale = $candidat_note_globale;
	}

	public function setEtablissementNom(?string $etablissement_nom): void
	{
		$this->etablissement_nom = $etablissement_nom;
	}

	public function setDiplomeLibelle(string $diplome_libelle): void
	{
		$this->diplome_libelle = $diplome_libelle;
	}

	public function setSpecicaliteSpe2(string $specicalite_spe2): void
	{
		$this->specicalite_spe2 = $specicalite_spe2;
	}

	public function setGroupeCouleur(string $groupe_couleur): void
	{
		$this->groupe_couleur = $groupe_couleur;
	}
}