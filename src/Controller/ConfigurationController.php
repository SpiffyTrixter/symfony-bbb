<?php

namespace App\Controller;

use App\DTO\Color;
use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\CarType;
use App\Repository\ConfigurationRepository;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfigurationController extends AbstractController
{
    const PAGE_TYPE = 'type';
    const PAGE_COLOR = 'color';
    const PAGE_NAME = 'name';
    const PAGE_SUMMARY = 'summary';
    const CAR_TYPE = 'carType';
    const CAR_COLOR = 'carColor';
    const CAR_NAME = 'carName';
    const CAR_ID = 'carId';

    private  Request $request;

    public function __construct(
        private readonly ConfigurationRepository $configurationRepository
    ) {}

    #[Route('/configuration/type', name: 'app_configuration_type')]
    public function type(Request $request): Response
    {
        $this->request = $request;

        if ($this->request->isMethod('POST')) {
            if ($this->setCarType()) {
                return $this->redirectToRoute('app_configuration_color');
            }

            $this->addFlash('danger', 'You must select a car type.');

            return $this->redirectToRoute('app_configuration_type');
        }

        return $this->render('configuration/type.html.twig', [
            'carTypes' => CarType::getCarTypes(),
            'carType' => $this->getCarType() ?: CarType::CUV
        ]);
    }

    #[Route('/configuration/color', name: 'app_configuration_color')]
    public function color(Request $request): Response
    {
        $this->request = $request;

        if ($this->getCarType() === null) {
            $this->addFlash('danger', 'You select a car type before choosing the color.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->request->isMethod('POST')) {
            if ($this->setCarColor()) {
                return $this->redirectToRoute('app_configuration_name');
            }

            $this->addFlash('danger', 'You must select a color.');

            return $this->redirectToRoute('app_configuration_color');
        }

        return $this->render('configuration/color.html.twig', [
            'carColor' => $this->getCarColor() ?: new Color(),
        ]);
    }

    #[Route('/configuration/name', name: 'app_configuration_name')]
    public function name(Request $request): Response
    {
        $this->request = $request;

        if ($this->getCarType() === null) {
            $this->addFlash('danger', 'You select a car type before choosing the color.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->getCarColor() === null) {
            $this->addFlash('danger', 'You select a color before choosing the name.');

            return $this->redirectToRoute('app_configuration_color');
        }

        if ($this->request->isMethod('POST')) {
            if ($this->setCarName()) {
                return $this->redirectToRoute('app_configuration_summary');
            }

            $this->addFlash('danger', 'You must name your car.');

            return $this->redirectToRoute('app_configuration_name');
        }

        return $this->render('configuration/name.html.twig', [
            'carName' => $this->getCarName()
        ]);
    }

    #[Route('/configuration/summary', name: 'app_configuration_summary')]
    public function summary(Request $request): Response
    {
        $this->request = $request;


        if ($this->getCarType() === null) {
            $this->addFlash('danger', 'You select a car type before seeing the summary.');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->getCarColor() === null) {
            $this->addFlash('danger', 'You select a color before seeing the summary.');

            return $this->redirectToRoute('app_configuration_color');
        }

        if ($this->getCarName() === null) {
            $this->addFlash('danger', 'You must name your car before seeing the summary.');

            return $this->redirectToRoute('app_configuration_name');
        }

        if ($request->isMethod('POST')) {
            $this->clearSession();

            return $this->redirectToRoute('app_configuration_type');
        }

        return $this->render('configuration/summary.html.twig', [
            'carType' => $this->getCarType(),
            'carColor' => $this->getCarColor(),
            'carName' => $this->getCarName(),
        ]);
    }

    #[Route('/configuration/save', name: 'app_configuration_save')]
    public function save(Request $request): RedirectResponse
    {
        $this->request = $request;

        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to save your car.');

            return $this->redirectToRoute('app_configuration_summary');
        }

        if ($this->getCarType() === null) {
            $this->addFlash('danger', 'You must select a car type before saving your car');

            return $this->redirectToRoute('app_configuration_type');
        }

        if ($this->getCarColor() === null) {
            $this->addFlash('danger', 'You must select a color before saving your car');
            return $this->redirectToRoute('app_configuration_color');
        }

        if ($this->getCarName() === null) {
            $this->addFlash('danger', 'You must name your car before saving it.');

            return $this->redirectToRoute('app_configuration_name');
        }

        $configuration = $this->getConfiguration();
        $configuration->setType($this->getCarType()->value);
        $configuration->setHexColor($this->getCarColor());
        $configuration->setName($this->getCarName());

        $this->clearSession();
        $this->configurationRepository->save($configuration, true);
        $this->addFlash('success', 'Your car has been saved.');

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_configuration_list');
        } else {
            return $this->redirectToRoute('app_configuration_user_list', [
                'username' => $this->getUser()->getUsername()
            ]);
        }
    }

    #[Route('/configuration/{slug}/load', name: 'app_configuration_load', requirements: ['slug' => '[a-z0-9-]+'])]
    public function load(Configuration $configuration, Request $request): RedirectResponse
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
    public function delete(Configuration $configuration): RedirectResponse
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
                'username' => $this->getUser()->getUsername()
            ]);
        }
    }

    #[Route('/configuration/{username}/list', name: 'app_configuration_user_list', requirements: ['username' => '[a-zA-Z0-9-]+'])]
    public function userList(User $user, Request $request): RedirectResponse|Response
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

        return $this->render('configuration/list.html.twig', compact('configurationsPager'));
    }

    #[Route('/configuration/list', name: 'app_configuration_list', priority: 2)]
    public function list(Request $request): RedirectResponse|Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_configuration_summary');
        }

        $configurationsPager = $this->configurationRepository->findAllPaginated(
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('configuration/list.html.twig', compact('configurationsPager'));
    }

    private function getCarType(): CarType|null
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->getSession()->has(self::CAR_TYPE)) {
            return null;
        }

        return CarType::getCarType($this->request->getSession()->get(self::CAR_TYPE));
    }

    private function setCarType(): bool
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->request->has('carType')) {
            return false;
        }

        $this->request->getSession()->set(self::CAR_TYPE, $this->request->request->get('carType'));

        return true;
    }

    private function getCarColor(): Color|null
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->getSession()->has(self::CAR_COLOR)) {
            return null;
        }

        return new Color(
            hex: $this->request->getSession()->get(self::CAR_COLOR)
        );
    }

    private function setCarColor(): bool
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (
            !$this->request->request->has('red') ||
            !$this->request->request->has('green') ||
            !$this->request->request->has('blue')
        ) {
            return false;
        }

        $color = new Color(
            red: $this->request->request->get('red'),
            green: $this->request->request->get('green'),
            blue: $this->request->request->get('blue')
        );

        $this->request->getSession()->set(self::CAR_COLOR, $color->getHex());

        return true;
    }

    private function getCarName(): string|null
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->getSession()->has(self::CAR_NAME)) {
            return null;
        }

        return $this->request->getSession()->get(self::CAR_NAME);
    }

    private function setCarName(): bool
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->request->has('carName')) {
            return false;
        }

        $this->request->getSession()->set(self::CAR_NAME, $this->request->request->get('carName'));

        return true;
    }

    private function getConfiguration(): Configuration
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        if (!$this->request->getSession()->has(self::CAR_ID)) {
            $configuration = new Configuration();
            $configuration->setOwner($this->getUser());

            return $configuration;
        }

        return $this->configurationRepository->find($this->request->getSession()->get(self::CAR_ID));
    }

    private function clearSession(): void
    {
        if (!isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        $this->request->getSession()->remove(self::CAR_ID);
        $this->request->getSession()->remove(self::CAR_TYPE);
        $this->request->getSession()->remove(self::CAR_COLOR);
        $this->request->getSession()->remove(self::CAR_NAME);
    }
}
