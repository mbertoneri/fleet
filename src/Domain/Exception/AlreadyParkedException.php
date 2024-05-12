<?php

namespace Fulll\Domain\Exception;

final class AlreadyParkedException extends \Exception
{
    public function __construct(string $plateNumber, float $latitude, float $longitude)
    {
        parent::__construct(
            message: sprintf('Vehicle with plate %s is already at location [lat:%f, ln:%f]',$plateNumber,$latitude,$longitude)
        );
    }
}