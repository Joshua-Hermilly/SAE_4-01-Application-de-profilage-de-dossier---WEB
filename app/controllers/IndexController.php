<?php
// app/controllers/IndexController.php

require_once '../app/core/Controller.php';

class IndexController extends Controller
{
    public function index(): void
    {
        $this->view('index');
    }
}
