<?php

namespace App\Services;

use App\Enums\EmissionType;
use App\Services\FlightEmissionService;
use App\Services\HotelEmissionService;
use App\Services\TrainEmissionService;
use App\Constants\ResponseMessage;
use InvalidArgumentException;

class EmissionServiceFactory
{
    public function make(string $type)
    {
        return match ($type) {
            EmissionType::FLIGHT => new FlightEmissionService(),
            EmissionType::HOTEL => new HotelEmissionService(),
            EmissionType::TRAIN => new TrainEmissionService(),
            default => throw new InvalidArgumentException(ResponseMessage::UNSUPPORTED_TYPE . $type)
        };
    }
}
