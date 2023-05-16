<?php

namespace App\Twig;

use App\Repository\UserRepository;
use DateTime;
use Pagerfanta\Pagerfanta;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('UserTable')]
final class UsersTableComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';
    #[LiveProp(writable: true)]
    public bool $hasConfigurations = false;
    #[LiveProp(writable: true, format: 'Y-m-d H:i:s')]
    public DateTime|null $createdAfter = null;
    #[LiveProp(writable: true, format: 'Y-m-d H:i:s')]
    public DateTime|null $updatedAfter = null;

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function getUsersPager(): Pagerfanta
    {
        return $this->userRepository->searchPaginated(
            $this->query,
            $this->createdAfter,
            $this->updatedAfter,
            $this->hasConfigurations,
            1,
            3
        );
    }
}
