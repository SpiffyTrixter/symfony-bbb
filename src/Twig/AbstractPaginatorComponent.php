<?php

namespace App\Twig;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

abstract class AbstractPaginatorComponent
{
    protected int $page = 1;
    protected int $maxPerPage = 3;

    #[LiveAction]
    public function nextPage(): void
    {
        $this->page++;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    #[LiveAction]
    public function toPage(#[LiveArg] int $page): void
    {
        $this->page = $page;
    }
}
