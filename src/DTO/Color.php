<?php

namespace App\DTO;

final class Color
{
    private int $red = 0;
    private int $green = 0;
    private int $blue = 0;

    public function __construct(string|null $hex = null, int|null $red = null, int|null $green = null, int|null $blue = null)
    {
        if ($hex) {
            $this->setHex($hex);
        }

        if ($red) {
            $this->setRed($red);
        }

        if ($green) {
            $this->setGreen($green);
        }

        if ($blue) {
            $this->setBlue($blue);
        }
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function setRed(int $red): void
    {
        $this->red = $red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function setGreen(int $green): void
    {
        $this->green = $green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }

    public function setBlue(int $blue): void
    {
        $this->blue = $blue;
    }

    public function getHex(): string
    {
        return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
    }

    public function setHex(string $hex): void
    {
        $hex = str_replace('#', '', $hex);

        $this->red = hexdec(substr($hex, 0, 2));
        $this->green = hexdec(substr($hex, 2, 2));
        $this->blue = hexdec(substr($hex, 4, 2));
    }

    public function __toString(): string
    {
        return $this->getHex();
    }
}
