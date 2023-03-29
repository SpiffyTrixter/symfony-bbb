<?php

namespace App\Controller;

use App\Entity\CustomCar;
use App\Entity\User;
use App\Repository\CustomCarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CustomCarController extends AbstractController
{
    public function __construct(
        private readonly CustomCarRepository $customCarRepository
    ) {}

    #[Route('/custom/car', name: 'app_custom_car')]
    public function index(): Response
    {
        return $this->render('custom_car/index.html.twig', [
            'controller_name' => 'CustomCarController',
        ]);
    }

    #[Route('/custom/car/{id}', name: 'app_custom_car_show')]
    public function show(CustomCar $customCar): Response
    {
        return $this->render('custom_car/show.html.twig', compact('customCar'));
    }

    #[Route('/custom/car/list/{id}', name: 'app_custom_car_list', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function list(int|null $userId): Response
    {
        if ($userId === null) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $customCars = $this->customCarRepository->findAll();
            } else {
                $customCars = $this->getUser()->getCustomCars();
            }
        } else {
            $customCars = $this->customCarRepository->findBy(['user' => $userId]);
        }

        dd($customCars);

        return $this->render('custom_car/list.html.twig', compact('customCars'));
    }
}
