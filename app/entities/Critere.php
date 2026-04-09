<?php

class Critere
{
	/*---------------------------------*/
	/*          Constructeur           */
	/*---------------------------------*/
	function __construct
	(
		private int    $critere_id,
		private string $critere_libelle,
		private string $critere_filtre,
		private float  $critere_min,
		private float  $critere_max,
	) {}

	/*---------------------------------*/
	/*             Getters             */
	/*---------------------------------*/
	public function getCritereId()
	{
		return $this->critere_id;
	}

	public function getCritereLibelle()
	{
		return $this->critere_libelle;
	}

	public function getCritereFiltre()
	{
		return $this->critere_filtre;
	}

	public function getCritereMin()
	{
		return $this->critere_min;
	}

	public function getCritereMax()
	{
		return $this->critere_max;
	}

	/*---------------------------------*/
	/*             Setters             */
	/*---------------------------------*/
	public function setCritereId($critere_id): void
	{
		$this->critere_id = $critere_id;
	}

	public function setCritereLibelle($critere_libelle): void
	{
		$this->critere_libelle = $critere_libelle;
	}

	public function setCritereFiltre($critere_filtre): void
	{
		$this->critere_filtre = $critere_filtre;
	}

	public function setCritereMin($critere_min): void
	{
		$this->critere_min = $critere_min;
	}

	public function setCritereMax($critere_max): void
	{
		$this->critere_max = $critere_max;
	}
}
