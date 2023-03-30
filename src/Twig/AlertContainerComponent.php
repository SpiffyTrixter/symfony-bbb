<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert-container')]
final class AlertContainerComponent
{
    public bool $dismissible = true;
    public array $alerts = [];
    public array $alertTypes = [
        'danger',
        'info',
        'success',
        'warning',
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        foreach ($this->alertTypes as $alertType) {
            $this->alerts[$alertType] = $this->requestStack->getSession()->getFlashBag()->get($alertType);
        }
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }

    public function hasAlerts(): bool
    {
        foreach ($this->alertTypes as $alertType) {
            if (count($this->alerts[$alertType]) > 0) {
                return true;
            }
        }

        return false;
    }
}
