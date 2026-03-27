<?php

class Compte
{
	/*╔════════════════════════╗*/
	/*║       Attributs        ║*/
	/*╚════════════════════════╝*/
	private int    $compte_id;
	private string $compte_nom;
	private string $compte_email;
	private string $compte_mdp;
	private string $compte_isAdmin;

	/*╔════════════════════════╗*/
	/*║     Constructeur       ║*/
	/*╚════════════════════════╝*/
	public function __construct( int $id, string $nom, string $email, string $mdp, string $isAdmin )
	{
		$this->compte_id      = $id;
		$this->compte_nom     = $nom;
		$this->compte_email   = $email;
		$this->compte_mdp     = $mdp;
		$this->compte_isAdmin = $isAdmin;
	}

	/*╔════════════════════════╗*/
	/*║    Serialisation       ║*/
	/*╚════════════════════════╝*/
	public function __serialize(): array
	{
		return [
			'compte_id'      => $this->compte_id,
			'compte_nom'     => $this->compte_nom,
			'compte_email'   => $this->compte_email,
			'compte_isAdmin' => $this->compte_isAdmin
		];
	}

	public function __unserialize(array $data): void
	{
		$this->compte_id      = $data['compte_id'     ];
		$this->compte_nom     = $data['compte_nom'    ];
		$this->compte_email   = $data['compte_email'  ];
		$this->compte_isAdmin = $data['compte_isAdmin'];
	}

	/*╔════════════════════════╗*/
	/*║   Getters/Setters      ║*/
	/*╚════════════════════════╝*/
	public function getCompteId(): int
	{
		return $this->compte_id;
	}

	public function setCompteId(int $compte_id): void
	{
		$this->compte_id = $compte_id;
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

	public function getCompteEmail(): string
	{
		return $this->compte_email;
	}

	public function setCompteEmail(string $compte_email): void
	{
		$this->compte_email = $compte_email;
	}

	public function getCompteNom(): string
	{
		return $this->compte_nom;
	}

	public function setCompteNom(string $compte_nom): void
	{
		$this->compte_nom = $compte_nom;
	}
}