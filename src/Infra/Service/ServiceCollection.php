<?php

namespace Fulll\Infra\Service;

use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommandHandler;
use Fulll\App\Command\ParkVehicle\ParkVehicleCommandHandler;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Query\FindFleet\FindFleetQueryHandler;
use Fulll\App\Query\FindVehicle\FindVehicleQueryHandler;
use Fulll\App\Shared\Command\CommandBusInterface;
use Fulll\App\Shared\Query\QueryBusInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Infra\Repository\FleetRepository;
use Fulll\Infra\Repository\VehicleRepository;
use Fulll\Infra\Shared\CommandBus;
use Fulll\Infra\Shared\QueryBus;
use Fulll\Infra\Sql\SqliteManager;
use Fulll\Infra\Sql\SqlManagerInterface;

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

        //sql
        $manager = new SqliteManager();
        $manager->connect(dsn: SqliteManager::DSN);
        $services->set(SqlManagerInterface::class, $manager);

        //register repositories
        $services->set(FleetRepositoryInterface::class, new FleetRepository($services));
        $services->set(VehicleRepositoryInterface::class, new VehicleRepository($services));

        //register bus
        $services->set(CommandBusInterface::class,new CommandBus($services));
        $services->set(QueryBusInterface::class,new QueryBus($services));

        //register commands
        $services->set(RegisterVehicleCommandHandler::class,new RegisterVehicleCommandHandler($services));
        $services->set(CreateFleetCommandHandler::class,new CreateFleetCommandHandler($services));
        $services->set(CreateVehicleCommandHandler::class,new CreateVehicleCommandHandler($services));
        $services->set(ParkVehicleCommandHandler::class,new ParkVehicleCommandHandler($services));

        //register queries
        $services->set(FindFleetQueryHandler::class, new FindFleetQueryHandler($services));
        $services->set(FindVehicleQueryHandler::class, new FindVehicleQueryHandler($services));

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

    public function getSqlManager(): SqlManagerInterface
    {
        return $this->services[SqlManagerInterface::class];
    }
}