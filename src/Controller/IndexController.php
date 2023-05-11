<?php

namespace App\Controller;

use App\Repository\ConfigurationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConfigurationRepository $configurationRepository,
    ) {}

    #[Route('/', name: 'app_index_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    #[Route('/admin', name: 'app_index_admin')]
    public function admin(): Response
    {
        $users = $this->userRepository->findBy([], ['createdAt' => 'DESC']);
        $configurations = $this->configurationRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('index/admin.html.twig', compact('users', 'configurations'));
    }
}
