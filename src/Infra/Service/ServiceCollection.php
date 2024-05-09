<?php

namespace Fulll\Infra\Service;

use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommandHandler;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Shared\Command\CommandBusInterface;
use Fulll\App\Shared\Query\QueryBusInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Infra\Repository\FleetRepository;
use Fulll\Infra\Repository\VehicleRepository;
use Fulll\Infra\Shared\CommandBus;
use Fulll\Infra\Shared\QueryBus;

final class ServiceCollection implements ServiceCollectionInterface
{
    //maybe find a better name

    private function __construct(
        private array $services
    ){
    }

    public static function create(): static
    {
        $services = new self([]);

        //register repositories
        $services->set(FleetRepositoryInterface::class, new FleetRepository());
        $services->set(VehicleRepositoryInterface::class, new VehicleRepository());

        //register bus
        $services->set(CommandBusInterface::class,new CommandBus($services));
        $services->set(QueryBusInterface::class,new QueryBus($services));

        //register commands
        $services->set(RegisterVehicleCommandHandler::class,new RegisterVehicleCommandHandler($services));
        $services->set(CreateFleetCommandHandler::class,new CreateFleetCommandHandler($services));
        $services->set(CreateVehicleCommandHandler::class,new CreateVehicleCommandHandler($services));

        return $services;

    }

    public function set(string $serviceId, object $value): static
    {
       $this->services[$serviceId] = $value ;
       return $this;
    }

    public function get(string $serviceId): ?object
    {
       return $this->services[$serviceId] ?? null;
    }

    public function getCommandBus(): CommandBusInterface
    {
        return $this->services[CommandBusInterface::class];
    }

    public function getQueryBus(): QueryBusInterface
    {
        return $this->services[QueryBusInterface::class];
    }
}