<?php

namespace Fulll\Domain\Domain;

use Fulll\Domain\Model\Fleet;

interface FleetRepositoryInterface
{
    public function save(Fleet $fleet): void;
    public function findByUserId(string $userId): ?Fleet;
}
