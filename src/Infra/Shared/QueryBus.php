<?php

namespace Fulll\Infra\Shared;

use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Query\QueryBusInterface;
use Fulll\App\Shared\Query\QueryInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;

final class QueryBus implements QueryBusInterface
{
    public function __construct(private ServiceCollectionInterface $serviceCollection) {}

    public function ask(QueryInterface $query): mixed
    {
        $serviceId = get_class($query) . 'Handler';

        $handler = $this->serviceCollection->get($serviceId);

        if  (null === $handler) {
            throw new ServiceNotFoundException($serviceId);
        }

        return ($handler)($query);
    }
}
