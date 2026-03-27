<?php

class Specialite
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	function __construct
	(
		private int     $specialite_id,
		private ?string $specialite_opt1,
		private ?string $specialite_opt2,
		private ?string $specialite_spe1,
		private ?string $specialite_spe2,
		private ?string $specialite_speAbd
	) {}


	/*---------------------------------*/
	/*             Getters             */
	/*---------------------------------*/
	public function getSpecialiteId(): int
	{
		return $this->specialite_id;
	}

	public function getSpecialiteOpt1(): ?string
	{
		return $this->specialite_opt1;
	}

	public function getSpecialiteOpt2(): ?string
	{
		return $this->specialite_opt2;
	}

	public function getSpecialiteSpe1(): ?string
	{
		return $this->specialite_spe1;
	}

	public function getSpecialiteSpe2(): ?string
	{
		return $this->specialite_spe2;
	}

	public function getSpecialiteSpeAbd(): ?string
	{
		return $this->specialite_speAbd;
	}


	/*---------------------------------*/
	/*             Setters             */
	/*---------------------------------*/
	public function setSpecialiteId(int $specialite_id): void
	{
		$this->specialite_id = $specialite_id;
	}

	public function setSpecialiteOpt1(?string $specialite_opt1): void
	{
		$this->specialite_opt1 = $specialite_opt1;
	}

	public function setSpecialiteOpt2(?string $specialite_opt2): void
	{
		$this->specialite_opt2 = $specialite_opt2;
	}

	public function setSpecialiteSpe1(?string $specialite_spe1): void
	{
		$this->specialite_spe1 = $specialite_spe1;
	}

	public function setSpecialiteSpe2(?string $specialite_spe2): void
	{
		$this->specialite_spe2 = $specialite_spe2;
	}

	public function setSpecialiteSpeAbd(?string $specialite_speAbd): void
	{
		$this->specialite_speAbd = $specialite_speAbd;
	}
}
