<?php
require_once '../app/controllers/DossierGetController.php';
require_once '../app/repositories/EtablissementRepository.php';

$controller = new DossierGetController();
$controller->getDossierCandidat();