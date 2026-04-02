<?php

class LocalisationService
{
	/*-------------------------------*/
	/* COLONNES                      */
	/*-------------------------------*/
	public const COLONNE =
	[
		'ID'           ,
		'Etablissement',
		'Pays'         ,
		'Departement'  ,
		'Code Postale' ,
		'Commune'
	];

	/*-------------------------------*/
	/* REPOSITORY                    */
	/*-------------------------------*/
	private $repoEtablissement;
	private $repoLocalisation;

	/*-------------------------------*/
	/* ENTITY                        */
	/*-------------------------------*/
	private array $etablissements;

	/*-------------------------------*/
	/* CONSTRUCT                     */
	/*-------------------------------*/
	function __construct()
	{
		
	}
}