<?php

namespace App\Twig;

use App\Entity\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('ConfigurationRow')]
final class ConfigurationRowComponent
{
    public Configuration $configuration;
    public string $targetModalId;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['configuration', 'targetModalId']);

        $resolver->setAllowedTypes('configuration', Configuration::class);
        $resolver->setAllowedTypes('targetModalId', 'string');

        return $resolver->resolve($data);
    }
}
