<?php

namespace App\Twig;

use App\Entity\Configuration;
use App\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('UserRow')]
final class UserRowComponent
{
    public User $user;
    public string $targetModalId;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['user', 'targetModalId']);

        $resolver->setAllowedTypes('user', User::class);
        $resolver->setAllowedTypes('targetModalId', 'string');

        return $resolver->resolve($data);
    }
}
