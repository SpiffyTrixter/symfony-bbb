<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigurationController extends AbstractController
{
    #[Route('/configuration', name: 'app_configuration_index')]
    public function index(): Response
    {
        return $this->render('configuration/index.html.twig');
    }
}
