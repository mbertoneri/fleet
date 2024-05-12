<?php

namespace Fulll\App\Exception;

final class ServiceNotFoundException extends \Exception
{
    public function __construct(string $serviceId)
    {
        parent::__construct(
            message: sprintf('Service with id %s was not found ', $serviceId)
        );
    }
}
