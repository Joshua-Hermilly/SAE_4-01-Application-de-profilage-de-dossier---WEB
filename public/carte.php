<?php
require_once '../app/controllers/CarteController.php';

$controller = new CarteController();

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $controller->getLocalisationByDistance();
}

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $controller->setLocalisation();
}