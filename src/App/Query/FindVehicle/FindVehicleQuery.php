<?php

namespace Fulll\App\Query\FindVehicle;

use Fulll\App\Shared\Query\QueryInterface;

class FindVehicleQuery implements QueryInterface
{
    public function __construct(public string $plateNumber)
    {}
}