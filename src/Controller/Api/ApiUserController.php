<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ApiUserController extends AbstractController
{
    #[Route(path: '/api/user/{id}', name: 'api_user', methods: ['GET'])]
    public function get(User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') || $this->getUser() !== $user) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        return $this->json($user);
    }

    #[Route(path: '/api/users', name: 'api_user_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        return $this->json($userRepository->findAll());
    }
}
