<?php

namespace Fulll\Domain\Exception;

use Fulll\Domain\Model\Vehicle;

final class VehicleAlreadyRegisteredException extends \Exception
{
    public function __construct(Vehicle $vehicle)
    {
        parent::__construct(
            message: sprintf('Vehicle %s is already registered', $vehicle->getPlateNumber())
        );
    }
}
