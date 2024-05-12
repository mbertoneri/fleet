<?php

namespace Fulll\Tests\Unit\App\CommandHandler;

use Fulll\App\Command\ParkVehicle\ParkVehicleCommand;
use Fulll\App\Command\ParkVehicle\ParkVehicleCommandHandler;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\AlreadyParkedException;
use Fulll\Domain\Model\Location;
use Fulll\Domain\Model\Vehicle;
use PHPUnit\Framework\TestCase;

final class ParkVehicleCommandHandlerUnitTest extends TestCase
{
    public function testLocalize(): void
    {
        $plateNumber = 'plateNumber';

        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->expects(static::once())->method('getLocation')->willReturn(null);

        $vehicleRepo = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepo->expects(static::once())->method('findByRegistrationPlate')->with($plateNumber)->willReturn($vehicle);
        $vehicleRepo->expects(static::once())->method('localize')->with($vehicle);

        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::once())->method('get')->with(VehicleRepositoryInterface::class)->willReturn($vehicleRepo);

        $command = new ParkVehicleCommand(vehiclePlateNumber: $plateNumber, longitude: 3.34, latitude: 6.55);

        $vehicle->expects(static::once())->method('setLocation')->with(new Location(6.55, 3.34));

        (new ParkVehicleCommandHandler($serviceCollection))($command);

    }

    public function testAlreadyLocalized(): void
    {
        $plateNumber = 'plateNumber';
        $location = new Location(6.55, 3.34);
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->expects(static::atLeastOnce())->method('getLocation')->willReturn($location);

        $vehicleRepo = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepo->expects(static::once())->method('findByRegistrationPlate')->with($plateNumber)->willReturn($vehicle);
        $vehicleRepo->expects(static::never())->method('localize');

        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::once())->method('get')->with(VehicleRepositoryInterface::class)->willReturn($vehicleRepo);

        $command = new ParkVehicleCommand(vehiclePlateNumber: $plateNumber, longitude: 3.34, latitude: 6.55);
        static::expectException(AlreadyParkedException::class);
        (new ParkVehicleCommandHandler($serviceCollection))($command);
    }

}
