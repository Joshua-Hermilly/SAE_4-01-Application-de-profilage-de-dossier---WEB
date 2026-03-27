<?php

require_once '../app/core/Repository.php';
require_once '../app/entities/Localisation.php';

class LocalisationRepository
{
	/*-------------------------------*/
	/*  Attributs                    */
	/*-------------------------------*/
    private $pdo;

	/*-------------------------------*/
	/*  Construct                    */
	/*-------------------------------*/
    public function __construct()
    {
        $this->pdo = Repository::getInstance()->getPDO();
    }

	/*-------------------------------*/
	/*  Requetes                     */
	/*-------------------------------*/
    private function createLocalisationFromRow(array $row): Localisation
    {
        return new Localisation(
            (int) $row['localisation_id'],
            $row['localisation_pays'],
            $row['localisation_code_postal'],
            $row['localisation_commune'],
            $row['localisation_departement']
        );
    }

    public function create(Localisation $localisation): void
    {
        $sql = "INSERT INTO LOCALISATION
                (localisation_pays, localisation_code_postal, localisation_commune, localisation_departement)
                VALUES (:pays, :code_postal, :commune, :departement)";

        $req = $this->pdo->prepare($sql);
        $req->bindValue(':pays'       , $localisation->getLocalisationPays       ());
        $req->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
        $req->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
        $req->bindValue(':departement', $localisation->getLocalisationDepartement());
        $req->execute();

        $localisation->setLocalisationId((int) $this->pdo->lastInsertId());
    }

    public function update(Localisation $localisation): void
    {
        $sql = "UPDATE LOCALISATION SET
                    localisation_pays        = :pays,
                    localisation_code_postal = :code_postal,
                    localisation_commune     = :commune,
                    localisation_departement = :departement
                WHERE localisation_id = :id";

        $req = $this->pdo->prepare($sql);
        $req->bindValue(':id'         , $localisation->getLocalisationId         ());
        $req->bindValue(':pays'       , $localisation->getLocalisationPays       ());
        $req->bindValue(':code_postal', $localisation->getLocalisationCodePostal ());
        $req->bindValue(':commune'    , $localisation->getLocalisationCommune    ());
        $req->bindValue(':departement', $localisation->getLocalisationDepartement());
        $req->execute();
    }
}
