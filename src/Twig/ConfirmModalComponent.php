<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ConfirmModal')]
final class ConfirmModalComponent
{
    public string $id = 'confirm-modal';
    public string $title = 'Confirm';
    public string $message = 'Are you sure?';
    public string $action = '#';
    public array $confirmButton = [
        'label' => 'Confirm',
        'class' => 'btn-primary',
    ];
    public array $cancelButton = [
        'label' => 'Cancel',
        'class' => 'btn-secondary',
    ];
}
