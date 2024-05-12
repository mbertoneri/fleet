<?php

namespace Fulll\Tests\Unit\Infra\Shared\QueryBus;

use Fulll\App\Exception\ServiceNotFoundException;
use Fulll\App\Shared\Query\QueryHandlerInterface;
use Fulll\App\Shared\Query\QueryInterface;
use Fulll\App\Shared\Service\ServiceCollectionInterface;
use Fulll\Infra\Shared\QueryBus;
use PHPUnit\Framework\TestCase;

final class QueryBusUnitTest extends TestCase
{
    public function testAskSuccess(): void
    {
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $query = $this->createMock(QueryInterface::class);

        $handler = $this->createMock(QueryHandlerInterface::class);
        $handler->expects(static::once())->method('__invoke')->with($query)->willReturn(new \stdClass());

        $serviceCollection->expects(static::once())->method('get')->willReturn($handler);

        (new QueryBus($serviceCollection))->ask($query);

    }

    public function testAskWithException(): void
    {
        $serviceCollection = $this->createMock(ServiceCollectionInterface::class);
        $query = $this->createMock(QueryInterface::class);

        $serviceCollection->expects(static::once())->method('get')->willReturn(null);

        static::expectException(ServiceNotFoundException::class);
        (new QueryBus($serviceCollection))->ask($query);
    }

}
