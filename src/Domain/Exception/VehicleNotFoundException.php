<?php

namespace Fulll\Domain\Exception;

class VehicleNotFoundException extends \Exception
{
    public function __construct(string $plateNumber)
    {
        parent::__construct(
            message: sprintf('Vehicle with plate %s was not found',$plateNumber)
        );
    }
}