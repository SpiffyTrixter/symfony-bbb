<?php

namespace App\Controller\Api;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ApiSecurityController extends AbstractController
{
    #[Route(path: '/api/login_check', name: 'api_login_check')]
    public function loginCheck(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
