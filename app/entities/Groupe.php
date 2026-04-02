<?php

class Groupe implements JsonSerializable
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private int     $groupe_id,
		private ?string $groupe_nom,
		private ?string $groupe_couleur,
		private ?float  $groupe_note_dossier,

		private array $criteres  = [],
		private array $candidats = []
	){}

	public function getHeader(): arrayw
	{
		return
			[
				'Id groupe'   ,
				'Nom groupe'  ,
				'Note Dossier',
				'Nombre Étudiants',
				'Couleur'
			];
	}

	/*-------------------------------*/
	/*  Serialize                    */
	/*-------------------------------*/
	public function jsonSerialize(): mixed { return $this->__serialize(); }

	public function __serialize()
	{
		return
			[
				'Id groupe'       => $this->getGroupeId         (),
				'Nom groupe'      => $this->getGroupeNom        (),
				'Note Dossier'    => $this->getGroupeNoteDossier(),
				'Nombre Étudiants'=> $this->getNbCandidats      (),
				'Couleur'         => $this->getGroupeCouleur    (),
			];
	}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getGroupeId(): int
	{
		return $this->groupe_id;
	}

	public function getGroupeNom(): ?string
	{
		return $this->groupe_nom;
	}

	public function getGroupeCouleur(): ?string
	{
		return $this->groupe_couleur;
	}

	public function getGroupeNoteDossier(): ?float
	{
		return $this->groupe_note_dossier;
	}

	public function getCriteres(): array
	{
		return $this->criteres;
	}

	public function getCandidats(): array
	{
		return $this->candidats;
	}

	public function getNbCandidats(): int
	{
		return count($this->candidats);
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setGroupeId(int $groupe_id): void
	{
		$this->groupe_id = $groupe_id;
	}

	public function setGroupeNom(?string $groupe_nom): void
	{
		$this->groupe_nom = $groupe_nom;
	}

	public function setGroupeCouleur(?string $groupe_couleur): void
	{
		$this->groupe_couleur = $groupe_couleur;
	}

	public function setGroupeNoteDossier(?float $groupe_note_dossoer): void
	{
		$this->groupe_note_dossier = $groupe_note_dossoer;
	}

	public function setCritere(array $criteres): void
	{
		$this->criteres = $criteres;
	}

	public function setCandidats(array $candidats): void
	{
		$this->candidats = $candidats;
	}
}