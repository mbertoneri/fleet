<?php

namespace Fulll\Tests\Unit\App\CommandHandler;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Model\Fleet;
use PHPUnit\Framework\TestCase;

class CreateFleetCommandHandlerUnitTest extends TestCase
{
    public function testHandle(): void
    {
        $fleetRepo = $this->createMock(FleetRepositoryInterface::class);
        $fleetRepo->expects(static::once())->method('save');
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $serviceCollection->expects(static::once())->method('get')->with(FleetRepositoryInterface::class)->willReturn($fleetRepo);

        $fleet = (new CreateFleetCommandHandler($serviceCollection))(new CreateFleetCommand('user'));

        static::assertSame('user', $fleet->getUserId());

    }
}
