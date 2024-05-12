<?php

namespace Fulll\Infra\Shared;

use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Command\CommandBusInterface;
use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Command\CommandInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;

final readonly class CommandBus implements CommandBusInterface
{
    public function __construct(private ServiceCollectionInterface $serviceCollection) {}

    public function execute(CommandInterface $command): mixed
    {
        $serviceId = get_class($command) . 'Handler';
        $handler = $this->serviceCollection->get($serviceId);

        if (null === $handler) {
            throw new ServiceNotFoundException($serviceId);
        }
        /** @var CommandHandlerInterface $handler */
        return ($handler)($command);
    }
}
