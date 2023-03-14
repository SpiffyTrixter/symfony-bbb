<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig');
    }
}
