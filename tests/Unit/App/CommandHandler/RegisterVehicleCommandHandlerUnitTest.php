<?php

namespace Fulll\Tests\Unit\App\CommandHandler;

use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;
use PHPUnit\Framework\TestCase;

final class RegisterVehicleCommandHandlerUnitTest extends TestCase
{
    public function testRegisterVehicle(): void
    {

        $fleet = $this->createMock(Fleet::class);
        $fleetRepo = $this->createMock(FleetRepositoryInterface::class);
        $fleetRepo->expects(static::once())->method('save')->with($fleet);
        $fleetRepo->expects(static::once())->method('findByUserId')->with('user')->willReturn($fleet);

        $vehicle = $this->createMock(Vehicle::class);

        $vehicleRepo = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepo->expects(static::once())->method('findByRegistrationPlate')->with('plateNumber')->willReturn($vehicle);

        $fleet->expects(static::once())->method('isVehicleRegistered')->with($vehicle)->willReturn(false);
        $fleet->expects(static::once())->method('registerVehicle')->with($vehicle);

        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::exactly(2))->method('get')->willReturnOnConsecutiveCalls($fleetRepo, $vehicleRepo);

        (new RegisterVehicleCommandHandler($serviceCollection))(new RegisterVehicleCommand(fleetUserId: 'user', vehiclePlateNumber: 'plateNumber'));

    }

    public function testAlreadyRegisteredVehicle(): void
    {

        $fleet = $this->createMock(Fleet::class);
        $fleetRepo = $this->createMock(FleetRepositoryInterface::class);
        $fleetRepo->expects(static::never())->method('save');
        $fleetRepo->expects(static::once())->method('findByUserId')->with('user')->willReturn($fleet);

        $vehicle = $this->createMock(Vehicle::class);

        $vehicleRepo = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepo->expects(static::once())->method('findByRegistrationPlate')->with('plateNumber')->willReturn($vehicle);

        $fleet->expects(static::once())->method('isVehicleRegistered')->with($vehicle)->willReturn(true);
        $fleet->expects(static::never())->method('registerVehicle');

        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::exactly(2))->method('get')->willReturnOnConsecutiveCalls($fleetRepo, $vehicleRepo);

        static::expectException(VehicleAlreadyRegisteredException::class);
        (new RegisterVehicleCommandHandler($serviceCollection))(new RegisterVehicleCommand(fleetUserId: 'user', vehiclePlateNumber: 'plateNumber'));

    }

}
