<?php

namespace App\Entity;

use App\Repository\CustomCarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomCarRepository::class)]
class CustomCar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 180)]
    private string|null $name = null;

    #[ORM\Column(length: 180)]
    private string|null $type = null;

    #[ORM\Column(length: 10)]
    private int|null $red = null;

    #[ORM\Column(length: 10)]
    private int|null $blue = null;

    #[ORM\Column(length: 10)]
    private string|null $green = null;

    #[ORM\ManyToOne(inversedBy: 'customCars')]
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

    public function getRed(): int|null
    {
        return $this->red;
    }

    public function setRed(int $red): self
    {
        $this->red = $red;

        return $this;
    }

    public function getBlue(): int|null
    {
        return $this->blue;
    }

    public function setBlue(int $blue): self
    {
        $this->blue = $blue;

        return $this;
    }

    public function getGreen(): int|null
    {
        return $this->green;
    }

    public function setGreen(int $green): self
    {
        $this->green = $green;

        return $this;
    }

    public function getColor(): array
    {
        return [
            'red' => $this->getRed(),
            'blue' => $this->getBlue(),
            'green' => $this->getGreen(),
        ];
    }

    public function setColor(array $color): self
    {
        $this->setRed($color['red']);
        $this->setBlue($color['blue']);
        $this->setGreen($color['green']);

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
