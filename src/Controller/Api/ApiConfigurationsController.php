<?php

namespace App\Controller\Api;

use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ApiConfigurationsController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationRepository $configurationRepository
    ) {}

    #[Route(path: '/api/configurations/types', name: 'api_configurations_types', methods: ['GET'])]
    public function types(): Response
    {
        $types = $this->configurationRepository->findAllTypes();

        return $this->json($this->prepareForMultiselect($types));
    }

    private function prepareForMultiselect(array $data, bool $child = false): array
    {
        $multiselect = [];

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $multiselect[] = [
                    'value' => reset($item),
                    'text' => end($item),
                ];

                continue;
            }

            if (is_int($key)) {
                $multiselect[] = [
                    'value' => $item,
                    'text' => $item,
                ];

                continue;
            }

            $multiselect[] = [
                'value' => $key,
                'text' => $item,
            ];
        }

        return $multiselect;
    }
}
