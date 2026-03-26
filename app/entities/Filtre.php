<?php

class Filtre
{
	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
	function __construct
	(
		private Groupe $groupe,
		private array  $criteres = []
	){}

	/*-------------------------------*/
	/*  Getters                      */
	/*-------------------------------*/
	public function getGroupe(): Groupe
	{
		return $this->groupe;
	}

	public function getCriteres(): array
	{
		return $this->criteres;
	}

	/*-------------------------------*/
	/*  Setters                      */
	/*-------------------------------*/
	public function setGroupe(Groupe $groupe): void
	{
		$this->groupe = $groupe;
	}

	public function setCriteres(array $criteres): void
	{
		$this->criteres = $criteres;
	}
}