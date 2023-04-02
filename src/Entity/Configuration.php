<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
#[Broadcast]
class Configuration
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 180)]
    private string|null $name = null;

    #[ORM\Column(length: 180)]
    private string|null $type = null;

    #[ORM\Column(length: 10)]
    private string|null $hexColor = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Gedmo\Slug(fields: ['name', 'type', 'id'])]
    private string|null $slug = null;

    #[ORM\ManyToOne(inversedBy: 'configurations')]
    #[ORM\JoinColumn(nullable: false)]
    private User|null $owner = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string|null
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHexColor(): string|null
    {
        return $this->hexColor;
    }

    public function setHexColor(string $hexColor): self
    {
        $this->hexColor = $hexColor;

        return $this;
    }

    public function getSlug(): string|null
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOwner(): User|null
    {
        return $this->owner;
    }

    public function setOwner(User|null $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
