<?php

namespace Fulll\Tests\Unit\App\CommandHandler;

use Fulll\App\Command\CreateVehicle\CreateVehicleCommand;
use Fulll\App\Command\CreateVehicle\CreateVehicleCommandHandler;
use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\VehicleRepositoryInterface;
use Fulll\Domain\Enum\VehicleTypeEnum;
use Fulll\Domain\Model\Vehicle;
use PHPUnit\Framework\TestCase;

final class CreateVehicleCommandHandlerUnitTest extends TestCase
{
    public function testHandle(): void
    {
        $vehicleRepo = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepo->expects(static::once())->method('save');
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::once())->method('get')->with(VehicleRepositoryInterface::class)->willReturn($vehicleRepo);

        $truck = (new CreateVehicleCommandHandler($serviceCollection))(new CreateVehicleCommand(registrationNumber: 'PX-3K-13', vehicleType: VehicleTypeEnum::TRUCK));

        static::assertSame('PX-3K-13', $truck->getPlateNumber());
        static::assertSame(VehicleTypeEnum::TRUCK, $truck->getType());
    }

    public function testWithException(): void
    {
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::once())->method('get')->with(VehicleRepositoryInterface::class)->willReturn(null);

        static::expectException(ServiceNotFoundException::class);
        (new CreateVehicleCommandHandler($serviceCollection))(new CreateVehicleCommand(registrationNumber: 'PX-3K-13', vehicleType: VehicleTypeEnum::TRUCK));
    }

}
