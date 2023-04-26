<?php

namespace App\Service;

use App\DTO\Color;
use App\Entity\Configuration;
use App\Enum\CarType;
use App\Repository\ConfigurationRepository;
use Symfony\Component\HttpFoundation\Request;

final class ConfigurationService
{
    const PAGE_TYPE = 'type';
    const PAGE_COLOR = 'color';
    const PAGE_NAME = 'name';
    const PAGE_SUMMARY = 'summary';
    const CAR_TYPE = 'carType';
    const CAR_COLOR = 'carColor';
    const CAR_NAME = 'carName';
    const CAR_ID = 'carId';

    public function __construct(
        private readonly ConfigurationRepository $configurationRepository
    ) {}

    public function getCarType(Request $request): CarType|null
    {
        if (!$request->getSession()->has(self::CAR_TYPE)) {
            return null;
        }

        return CarType::getCarType($request->getSession()->get(self::CAR_TYPE));
    }

    public function setCarType(Request $request): bool
    {
        if (!$request->request->has('carType')) {
            return false;
        }

        $request->getSession()->set(self::CAR_TYPE, $request->request->get('carType'));

        return true;
    }

    public function getCarColor(Request $request): Color|null
    {
        if (!$request->getSession()->has(self::CAR_COLOR)) {
            return null;
        }

        return new Color(
            hex: $request->getSession()->get(self::CAR_COLOR)
        );
    }

    public function setCarColor(Request $request): bool
    {
        if (
            !$request->request->has('red') ||
            !$request->request->has('green') ||
            !$request->request->has('blue')
        ) {
            return false;
        }

        $color = new Color(
            red: $request->request->get('red'),
            green: $request->request->get('green'),
            blue: $request->request->get('blue')
        );

        $request->getSession()->set(self::CAR_COLOR, $color->getHex());

        return true;
    }

    public function getCarName(Request $request): string|null
    {
        if (!$request->getSession()->has(self::CAR_NAME)) {
            return null;
        }

        return $request->getSession()->get(self::CAR_NAME);
    }

    public function setCarName(Request $request): bool
    {
        if (!$request->request->has('carName')) {
            return false;
        }

        $request->getSession()->set(self::CAR_NAME, $request->request->get('carName'));

        return true;
    }

    public function getConfiguration(Request $request): Configuration
    {
        if (!$request->getSession()->has(self::CAR_ID)) {
            $configuration = new Configuration();
            $configuration->setOwner($request->getUser());

            return $configuration;
        }

        return $this->configurationRepository->find($request->getSession()->get(self::CAR_ID));
    }

    public function clearConfiguration(Request $request): void
    {
        $request->getSession()->remove(self::CAR_ID);
        $request->getSession()->remove(self::CAR_TYPE);
        $request->getSession()->remove(self::CAR_COLOR);
        $request->getSession()->remove(self::CAR_NAME);
    }
}
