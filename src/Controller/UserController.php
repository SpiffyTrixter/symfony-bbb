<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_index', priority: 2)]
    public function index(): Response
    {
        return $this->redirect($this->generateUrl('app_user_profile', [
            'username' => $this->getUser()->getUserIdentifier()
        ]));
    }

    #[Route('/{username}/profile', name: 'app_user_profile', requirements: ['username' => '[a-zA-Z0-9-]+'])]
    public function profile(User $user): Response
    {
        if ($user !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only view your own profile.');

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/index.html.twig', compact('user'));
    }
}
