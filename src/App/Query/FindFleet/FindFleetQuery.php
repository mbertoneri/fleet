<?php

namespace Fulll\App\Query\FindFleet;

use Fulll\App\Shared\Query\QueryInterface;

final readonly class FindFleetQuery implements QueryInterface
{
    public function __construct(
        public string $userId
    ){
    }
}