<?php

namespace Fulll\Tests\Unit\Infra\Service;

use Fulll\Infra\Service\ServiceCollection;
use PHPUnit\Framework\TestCase;

final class ServiceCollectionUnitTest extends TestCase
{
    public function testService(): void
    {
        $service = ServiceCollection::create();

        $service->set(DummyService::class, new DummyService());
        static::assertInstanceOf(DummyService::class, $service->get(DummyService::class));

    }
}

class DummyService {}
