<?php

class Export
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private  string $candidat_code                ,
		private  string $candidat_nom                 ,
		private  string $candidat_prenom              ,
		private  string $candidat_civilite            ,
		private  string $candidat_profil              ,
		private  string $candidat_boursier_code        ,
		private ?string $formation_filiere        = "",
		private ?string $formation_libelle        = "",
		private ?string $specialite_mention       = "",
		private ?string $etablissement_nom        = "",
		private ?string $localisation_commune     = "",
		private ?string $localisation_code_postal = "",
		private ?string $localisation_departement = "",
		private ?string $localisation_pays        = "",
		private ?string $diplome_type_code        = "",
		private ?string $diplome_type_libelle     = "",
		private ?string $diplome_serie_code       = "",
		private ?string $diplome_serie_libelle    = "",
		private ?string $specialite_opt1          = "",
		private ?string $specialite_spe1          = "",
		private ?string $specialite_spe2          = "",
		private ?string $specialite_spe3          = "",
		private ?string $specialite_speabd        = "",
		private ?string $candidat_note_globale    = "",
		private ?string $candidat_note_fiche      = "",
		private ?string $candidat_note_lycee      = "",
		private ?string $groupe_note_dossier      = "",
		private ?string $candidat_commentaire     = ""
	){}

	/*-------------------------------*/
	/*  Export                       */
	/*-------------------------------*/
	public function getDonnees()
	{
		return
		[
			$this->candidat_code            ,
			$this->candidat_nom             ,
			$this->candidat_prenom          ,
			$this->candidat_civilite        ,
			$this->candidat_profil          ,
			$this->candidat_boursier_code   ,
			$this->formation_filiere        ,
			$this->formation_libelle        ,
			$this->specialite_mention       ,
			$this->etablissement_nom        ,
			$this->localisation_commune     ,
			$this->localisation_code_postal ,
			$this->localisation_departement ,
			$this->localisation_pays        ,
			$this->diplome_type_code        ,
			$this->diplome_type_libelle     ,
			$this->diplome_serie_code       ,
			$this->diplome_serie_libelle    ,
			$this->specialite_opt1          ,
			$this->specialite_spe1 +"/"+ $this->specialite_spe2 + "/"+$this->specialite_spe3 ,
			$this->specialite_speabd        ,
			$this->candidat_note_globale    ,
			$this->candidat_note_fiche      ,
			$this->candidat_note_lycee      ,
			$this->groupe_note_dossier      ,
			$this->candidat_commentaire     ,
		];
	}

}