<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert')]
final class AlertComponent
{
    public string $type = 'success';
    public string $message;
    public bool $dismissible = true;

    public function getIconClass(): string
    {
        return match ($this->type) {
            'danger', 'warning' => 'fa fa-circle-exclamation',
            'info' => 'fa fa-circle-info',
            default => 'fa fa-circle-check',
        };
    }
}
