<?php

namespace App\Controller;

use App\Entity\CustomCar;
use App\Enum\CarType;
use App\Repository\CustomCarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigurationController extends AbstractController
{
    #[Route('/configuration/type', name: 'app_configuration_type')]
    public function type(Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            if ($request->request->has('carType')) {
                $session->set('carType', $request->request->get('carType'));
            }

            return $this->redirectToRoute('app_configuration_color');
        }

        $carTypes = CarType::getCarTypes();
        $carType = CarType::getCarType($session->get('carType')) ?: CarType::micro;

        return $this->render('configuration/type.html.twig', compact('carTypes', 'carType'));
    }

    #[Route('/configuration/color', name: 'app_configuration_color')]
    public function color(Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            if ($request->request->has('red') || $request->request->has('blue') || $request->request->has('green')) {
                $session->set('carColor', [
                    'red' => $request->request->get('red') ?: 0,
                    'blue' => $request->request->get('blue') ?: 0,
                    'green' => $request->request->get('green') ?: 0,
                ]);
            }

            return $this->redirectToRoute('app_configuration_name');
        }

        if (!$session->has('carType')) {
            $this->addFlash('danger', 'You must configure your car before choosing the color.');

            return $this->redirectToRoute('app_configuration_type');
        }

        $carColor = $session->get('carColor') ?: [
            'red' => 0,
            'blue' => 0,
            'green' => 0,
        ];

        return $this->render('configuration/color.html.twig', compact('carColor'));
    }

    #[Route('/configuration/name', name: 'app_configuration_name')]
    public function name(Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            if ($request->request->has('carName')) {
                $session->set('carName', $request->request->get('carName'));
            }

            return $this->redirectToRoute('app_configuration_summary');
        }

        if (!$session->has('carType') || !$session->has('carColor')) {
            $this->addFlash('danger', 'You must configure your car before naming it.');

            return $this->redirectToRoute('app_configuration_type');
        }

        $carType = CarType::getCarType($session->get('carType'));
        $carColor = $session->get('carColor');
        $carName = $session->get('carName') ?: '';

        return $this->render('configuration/name.html.twig', compact('carName'));
    }

    #[Route('/configuration/summary', name: 'app_configuration_summary')]
    public function summary(Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            $session->remove('carType');
            $session->remove('carColor');
            $session->remove('carName');

            return $this->redirectToRoute('app_configuration_type');
        }

        if (!$session->has('carType') || !$session->has('carColor') || !$session->has('carName')) {
            $this->addFlash('danger', 'You must configure your car before seeing the summary.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($session->has('carName') && $session->get('carName') === '') {
            $this->addFlash('danger', 'You must name your car before seeing the summary.');

            return $this->redirectToRoute('app_configuration_name');
        }

        $carType = CarType::getCarType($session->get('carType'));
        $carColor = $session->get('carColor');
        $carName = $session->get('carName');

        return $this->render('configuration/summary.html.twig', compact('carName', 'carColor', 'carType'));
    }

    #[Route('/configuration/save', name: 'app_configuration_save')]
    public function save(Request $request, CustomCarRepository $customCarRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to save your car.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        $session = $request->getSession();

        if (!$session->has('carType') || !$session->has('carColor') || !$session->has('carName')) {
            $this->addFlash('danger', 'You must configure your car before saving it.');

            return $this->redirectToRoute('app_configuration_type');
        }

        $customCar = new CustomCar();
        $customCar->setType($session->get('carType'));
        $customCar->setColor($session->get('carColor'));
        $customCar->setName($session->get('carName'));
        $customCar->setOwner($this->getUser());

        $customCarRepository->save($customCar, true);

        $session->remove('carType');
        $session->remove('carColor');
        $session->remove('carName');

        dd($customCar);
    }
}
