<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ConfigurationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConfigurationRepository $configurationRepository,
    ) {}

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

    #[Route('/{username}/edit', name: 'app_user_edit', requirements: ['username' => '[a-zA-Z0-9-]+'])]
    public function edit(Request $request, User $user): Response
    {
        if ($user !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only edit your own profile.');

            return $this->redirectToRoute('app_user_index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->userRepository->save($user, true);

            $this->addFlash('success', 'User updated successfully');

            return $this->redirectToRoute('app_user_profile', [
                'username' => $user->getUserIdentifier()
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'userForm' => $form,
        ]);
    }

    #[Route('/{username}/delete', name: 'app_user_delete', requirements: ['username' => '[a-zA-Z0-9-]+'])]
    public function delete(User $user, Security $security): Response
    {
        if ($user !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only delete your own profile.');

            return $this->redirectToRoute('app_user_index');
        }

        $this->configurationRepository->removeByUser($user, true);
        $this->userRepository->remove($user, true);

        if ($user === $this->getUser()) {
            $this->addFlash('success', 'Your account has been deleted.');
            $security->logout();
        } else {
            $this->addFlash(
                'success',
                sprintf('The account (%s) has been deleted.', $user->getUserIdentifier())
            );
        }

        return $this->redirectToRoute('app_index_index');
    }

    #[Route('/users', name: 'app_user_users', priority: 2)]
    public function users(Request $request): Response
    {
        $usersPager = $this->userRepository->findAllPaginated(
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('user/users.html.twig', compact('usersPager'));
    }
}
