<?php

namespace Fulll\Tests\Integration;

use Fulll\Infra\Sql\SqlManagerInterface;

trait ResetTrait
{
    public function reset(SqlManagerInterface $manager): void
    {
        $manager->executeStmt('DELETE FROM fleet_vehicle');
        $manager->executeStmt('DELETE FROM vehicle');
        $manager->executeStmt('DELETE FROM fleet');
    }
}
