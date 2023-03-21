<?php

namespace App\Controller;

use LogicException;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ApiController extends AbstractController
{

    #[Route(path: '/api/login', name: 'app_api_login', methods: ['GET'])]
    public function login(): Response
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/api/user', name: 'app_api_user', methods: ['GET'])]
    public function user(#[CurrentUser] User|null $user): Response
    {
        return $this->json(compact('user'));
    }
}
