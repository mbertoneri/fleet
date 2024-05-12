<?php

namespace Fulll\App\Shared\Service;

use Fulll\App\Shared\Command\CommandBusInterface;
use Fulll\App\Shared\Query\QueryBusInterface;
use Fulll\Infra\Sql\SqlManagerInterface;

interface ServiceCollectionInterface
{
    public function set(string $serviceId, object $value): static;
    public function get(string $serviceId): ?object;

    public function getCommandBus(): CommandBusInterface;
    public function getQueryBus(): QueryBusInterface;
    public function getSqlManager(): SqlManagerInterface;

}
