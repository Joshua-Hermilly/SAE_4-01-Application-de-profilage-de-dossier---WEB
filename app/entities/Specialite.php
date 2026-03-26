<?php

class Specialite
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	function __construct(
		private ?int    $specialite_id,
		private ?string $specialite_nom
	) {}

	/*---------------------------------*/
	/*             Getters             */
	/*---------------------------------*/
	public function getSpecialiteId()
	{
		return $this->specialite_id;
	}

	public function getSpecialiteNom()
	{
		return $this->specialite_nom;
	}

	/*---------------------------------*/
	/*             Setters             */
	/*---------------------------------*/
	public function setSpecialiteId($specialite_id): self
	{
		$this->specialite_id = $specialite_id;
		return $this;
	}

	public function setSpecialiteNom($specialite_nom): self
	{
		$this->specialite_nom = $specialite_nom;
		return $this;
	}
}
