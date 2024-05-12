<?php

namespace Fulll\Domain\Exception;

use Fulll\Domain\Model\Fleet;
use Fulll\Domain\Model\Vehicle;

final class FleetNotFoundException extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct(
            message: sprintf('Fleet with id %s was not found ',$id)
        );
    }
}