<?php

namespace App\Enums;

class EmissionType
{
    const FLIGHT = 'flight';
    const HOTEL = 'hotel';
    const TRAIN = 'train';

    public static function values(): array
    {
        return [
            self::FLIGHT,
            self::HOTEL,
            self::TRAIN,
        ];
    }
}
