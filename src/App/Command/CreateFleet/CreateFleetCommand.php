<?php

namespace Fulll\App\Command\CreateFleet;

use Fulll\App\Shared\Command\CommandInterface;

final readonly class CreateFleetCommand implements CommandInterface
{
    public function __construct(public string $userId) {}
}
