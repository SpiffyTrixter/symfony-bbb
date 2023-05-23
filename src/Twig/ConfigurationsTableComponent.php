<?php

namespace App\Twig;

use App\Repository\ConfigurationRepository;
use DateTime;
use Pagerfanta\Pagerfanta;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('ConfigurationsTable')]
final class ConfigurationsTableComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string|null $query = null;

    #[LiveProp(writable: true)]
    public array|null $types = null;

    #[LiveProp(writable: true)]
    public string|null $color = null;

    #[LiveProp(writable: true, format: 'Y-m-d H:i:s')]
    public DateTime|null $createdAfter = null;

    #[LiveProp(writable: true, format: 'Y-m-d H:i:s')]
    public DateTime|null $updatedAfter = null;

    public function __construct(
        private readonly ConfigurationRepository $configurationRepository
    ) {}

    public function getConfigurationsPager(): Pagerfanta
    {
        return $this->configurationRepository->searchPaginated(
            $this->query,
            $this->color,
            $this->types,
            $this->createdAfter,
            $this->updatedAfter,
            1,
            3
        );
    }
}
