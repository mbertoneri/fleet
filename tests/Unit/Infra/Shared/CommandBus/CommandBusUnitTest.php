<?php

namespace Fulll\Tests\Unit\Infra\Shared\CommandBus;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Command\CommandHandlerInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Infra\Shared\CommandBus;
use PHPUnit\Framework\TestCase;

final class CommandBusUnitTest extends TestCase
{
    public function testExecute(): void
    {
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);

        $command = new CreateFleetCommand('user');

        $handler = $this->createMock(CommandHandlerInterface::class);
        $handler->expects(static::once())->method('__invoke')->with($command)->willReturn(new \stdClass());

        $serviceCollection
            ->expects(static::once())
            ->method('get')
            ->with(static::callback(static function (string $command): bool {
                static::assertTrue(str_ends_with($command, 'CreateFleetCommandHandler'));
                return true;
            }))
            ->willReturn($handler);

        (new CommandBus($serviceCollection))->execute($command);

    }

    public function testExecuteException(): void
    {
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);

        $command = new CreateFleetCommand('user');

        $serviceCollection
            ->expects(static::once())
            ->method('get')
            ->willReturn(null);

        static::expectException(ServiceNotFoundException::class);
        (new CommandBus($serviceCollection))->execute($command);

    }
}
