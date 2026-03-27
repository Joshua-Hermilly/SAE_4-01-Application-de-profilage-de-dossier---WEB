<?php

class Compte
{
	/*╔════════════════════════╗*/
	/*║       Attributs        ║*/
	/*╚════════════════════════╝*/
	private  string $compte_identifiant;
	private  string $compte_mdp;
	private  string $compte_isAdmin;

	/*╔════════════════════════╗*/
	/*║     Constructeur       ║*/
	/*╚════════════════════════╝*/
	public function __construct( string $identifiant, string $mdp, string $isAdmin )
	{
		$this->compte_identifiant = $identifiant;
		$this->compte_mdp         = $mdp;
		$this->compte_isAdmin     = $isAdmin;
	}

	/*╔════════════════════════╗*/
	/*║    Serialisation       ║*/
	/*╚════════════════════════╝*/
	public function __serialize(): array
	{
		return [
			'compte_identifiant' => $this->compte_identifiant,
			'compte_isAdmin'     => $this->compte_isAdmin
		];
	}

	public function __unserialize(array $data): void
	{
		$this->compte_identifiant = $data['compte_identifiant'];
		$this->compte_isAdmin     = $data['compte_isAdmin'    ];
	}

	/*╔════════════════════════╗*/
	/*║   Getters/Setters      ║*/
	/*╚════════════════════════╝*/
	public function getCompteIdentifiant(): int
	{
		return $this->compte_identifiant;
	}

	public function setCompteId(int $compte_identifiant): void
	{
		$this->compte_identifiant = $compte_identifiant;
	}

	public function getCompteIsAdmin(): string
	{
		return $this->compte_isAdmin;
	}

	public function setCompteIsAdmin(string $compte_isAdmin): void
	{
		$this->compte_isAdmin = $compte_isAdmin;
	}

	public function getCompteMdp(): string
	{
		return $this->compte_mdp;
	}

	public function setCompteMdp(string $compte_mdp): void
	{
		$this->compte_mdp = $compte_mdp;
	}
}