<?php

namespace App\Twig;

use App\Repository\ConfigurationRepository;
use DateTime;
use Pagerfanta\Pagerfanta;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('ConfigurationsTable')]
final class ConfigurationsTableComponent extends AbstractPaginatorComponent
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

    #[LiveAction]
    public function resetFilters(): void
    {
        $this->page = 1;
        $this->query = null;
        $this->types = null;
        $this->color = null;
        $this->createdAfter = null;
        $this->updatedAfter = null;
    }

    public function getConfigurationsPager(): Pagerfanta
    {
        dump($this->page);
        return $this->configurationRepository->searchPaginated(
            $this->query,
            $this->color,
            $this->types,
            $this->createdAfter,
            $this->updatedAfter,
            $this->page,
            $this->maxPerPage
        );
    }
}
