<?php
// app/controllers/IndexController.php

require_once '../app/core/Controller.php';

class IndexController extends Controller
{
    public function index(): void
    {
		if (session_status() === PHP_SESSION_NONE)
		{
            session_start();
        }

        if (!isset($_SESSION['account']))
		{
            $this->redirectTo('login.php');
            return;
        }

        $months =
			[
            1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
        ];
        $now = new DateTime();
        $initDate = $now->format('d') . ' '
            . $months[(int) $now->format('n')] . ' '
            . $now->format('Y');

        $this->view('index', 'SAE S401 - Développement d\'une application complexe', [
            'project_name' => 'MonProjet',
            'initDate'     => $initDate,
        ]);
    }
}
