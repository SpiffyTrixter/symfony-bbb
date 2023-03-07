<?php

namespace App\Enum;

enum CarType: string
{
    case CUV = 'cuv';
    case micro = 'micro';
    case sedan = 'sedan';
    case SUV = 'suv';

    public static function getCarTypes(): array
    {
        return [
            self::CUV,
            self::micro,
            self::sedan,
            self::SUV,
        ];
    }

    public static function getCarType(string|null $carType): self|null
    {
        foreach (self::getCarTypes() as $type) {
            if ($type->value === $carType) {
                return $type;
            }
        }

        return null;
    }
}
