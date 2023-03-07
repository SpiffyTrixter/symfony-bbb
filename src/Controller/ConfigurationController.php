<?php

namespace App\Controller;

use App\Enum\CarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigurationController extends AbstractController
{
    #[Route('/configuration', name: 'app_configuration_type')]
    public function type(Request $request): Response
    {
        $session = $request->getSession();
        $carTypes = CarType::getCarTypes();
        $carType = CarType::getCarType($session->get('carType') ?? CarType::CUV->value);

        return $this->render('configuration/type.html.twig', compact('carTypes', 'carType'));
    }

    #[Route('/configuration/color', name: 'app_configuration_color')]
    public function color(Request $request): Response
    {
        $session = $request->getSession();
        $carColor = $session->get('carColor') ?: ['red' => 0, 'blue' => 0, 'green' => 0];
        $carType = $request->query->get('type');

        if ($carType !== null) {
            $session->set('carType', $carType);
        }

        return $this->render('configuration/color.html.twig', compact('carColor'));
    }

    #[Route('/configuration/name', name: 'app_configuration_name')]
    public function name(Request $request): Response
    {
        $session = $request->getSession();
        $carName = $session->get('carName');
        $carColor = $request->query->get('color');

        if ($carColor !== null) {
            $session->set('carColor', $carColor);
        }

        return $this->render('configuration/name.html.twig', compact('carName'));
    }
}
