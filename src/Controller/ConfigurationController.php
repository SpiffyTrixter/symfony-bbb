<?php

namespace App\Controller;

use App\DTO\Color;
use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\CarType;
use App\Repository\ConfigurationRepository;
use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigurationController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly ConfigurationRepository $configurationRepository,
    ) {}

    #[Route('/configuration/type', name: 'app_configuration_type')]
    public function type(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->configurationService->setCarType($request)) {
                return $this->redirectToRoute('app_configuration_color');
            }

            $this->addFlash('danger', 'You must select a car type.');

            return $this->redirectToRoute('app_configuration_type');
        }

        return $this->render('configuration/type.html.twig', [
            'carTypes' => CarType::getCarTypes(),
            'carType' => $this->configurationService->getCarType($request) ?: CarType::CUV
        ]);
    }

    #[Route('/configuration/color', name: 'app_configuration_color')]
    public function color(Request $request): Response
    {
        if ($this->configurationService->getCarType($request) === null) {
            $this->addFlash('danger', 'You select a car type before choosing the color.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($request->isMethod('POST')) {
            if ($this->configurationService->setCarColor($request)) {
                return $this->redirectToRoute('app_configuration_name');
            }

            $this->addFlash('danger', 'You must select a color.');

            return $this->redirectToRoute('app_configuration_color');
        }

        return $this->render('configuration/color.html.twig', [
            'carColor' => $this->configurationService->getCarColor($request) ?: new Color(),
        ]);
    }

    #[Route('/configuration/name', name: 'app_configuration_name')]
    public function name(Request $request): Response
    {
        if ($this->configurationService->getCarType($request) === null) {
            $this->addFlash('danger', 'You select a car type before choosing the color.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->configurationService->getCarColor($request) === null) {
            $this->addFlash('danger', 'You select a color before choosing the name.');

            return $this->redirectToRoute('app_configuration_color');
        }

        if ($request->isMethod('POST')) {
            if ($this->configurationService->setCarName($request)) {
                return $this->redirectToRoute('app_configuration_summary');
            }

            $this->addFlash('danger', 'You must name your car.');

            return $this->redirectToRoute('app_configuration_name');
        }

        return $this->render('configuration/name.html.twig', [
            'carName' => $this->configurationService->getCarName($request)
        ]);
    }

    #[Route('/configuration/summary', name: 'app_configuration_summary')]
    public function summary(Request $request): Response
    {
        if ($this->configurationService->getCarType($request) === null) {
            $this->addFlash('danger', 'You select a car type before seeing the summary.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->configurationService->getCarColor($request) === null) {
            $this->addFlash('danger', 'You select a color before seeing the summary.');

            return $this->redirectToRoute('app_configuration_color');
        }

        if ($this->configurationService->getCarName($request) === null) {
            $this->addFlash('danger', 'You must name your car before seeing the summary.');

            return $this->redirectToRoute('app_configuration_name');
        }

        if ($request->isMethod('POST')) {
            $this->configurationService->clearConfiguration($request);

            return $this->redirectToRoute('app_configuration_type');
        }

        return $this->render('configuration/summary.html.twig', [
            'carType' => $this->configurationService->getCarType($request),
            'carColor' => $this->configurationService->getCarColor($request),
            'carName' => $this->configurationService->getCarName($request),
        ]);
    }

    #[Route('/configuration/save', name: 'app_configuration_save')]
    public function save(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to save your car.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        if ($this->configurationService->getCarType($request) === null) {
            $this->addFlash('danger', 'You must select a car type before saving your car');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->configurationService->getCarColor($request) === null) {
            $this->addFlash('danger', 'You must select a color before saving your car');
            return $this->redirectToRoute('app_configuration_color');
        }

        if ($this->configurationService->getCarName($request) === null) {
            $this->addFlash('danger', 'You must name your car before saving it.');

            return $this->redirectToRoute('app_configuration_name');
        }

        $configuration = $this->configurationService->getConfiguration($request);
        $configuration->setType($this->configurationService->getCarType($request)->value);
        $configuration->setHexColor($this->configurationService->getCarColor($request));
        $configuration->setName($this->configurationService->getCarName($request));

        $this->configurationService->clearConfiguration($request);
        $this->configurationRepository->save($configuration, true);
        $this->addFlash('success', 'Your car has been saved.');

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_configuration_list');
        } else {
            return $this->redirectToRoute('app_configuration_user_list', [
                'username' => $this->getUser()->getUserIdentifier()
            ]);
        }
    }

    #[Route('/configuration/{slug}/load', name: 'app_configuration_load', requirements: ['slug' => '[a-z0-9-]+'])]
    public function load(Configuration $configuration, Request $request): Response
    {
        if ($configuration->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only load your own car.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        $session = $request->getSession();
        $session->set('carId', $configuration->getId());
        $session->set('carType', $configuration->getType());
        $session->set('carColor', $configuration->getHexColor());
        $session->set('carName', $configuration->getName());

        if ($request->get('page')) {
            return $this->redirectToRoute('app_configuration_' . $request->get('page'));
        }

        return $this->redirectToRoute('app_configuration_type');
    }

    #[Route('/configuration/{slug}/delete', name: 'app_configuration_delete', requirements: ['slug' => '[a-z0-9-]+'])]
    public function delete(Configuration $configuration): Response
    {
        if ($configuration->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only delete your own car.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        $this->configurationRepository->remove($configuration, true);

        $this->addFlash('success', 'Your car has been deleted.');

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_configuration_list');
        } else {
            return $this->redirectToRoute('app_configuration_user_list', [
                'username' => $this->getUser()->getUserIdentifier()
            ]);
        }
    }

    #[Route('/{username}/configurations', name: 'app_configuration_user_list', requirements: ['username' => '[a-zA-Z0-9-]+'])]
    public function userList(User $user, Request $request): Response
    {
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You can only see your own cars.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        $configurationsPager = $this->configurationRepository->findByUserPaginated(
            $user,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('configuration/configurations.html.twig', compact('configurationsPager'));
    }

    #[Route('/configurations', name: 'app_configuration_list', priority: 2)]
    public function list(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_configuration_user_list', [
                'username' => $this->getUser()->getUserIdentifier()
            ]);
        }

        $configurationsPager = $this->configurationRepository->findAllPaginated(
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('configuration/configurations.html.twig', compact('configurationsPager'));
    }
}
