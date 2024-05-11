<?php

namespace Fulll\Domain\Domain;

use Fulll\Domain\Model\Fleet;

interface FleetRepositoryInterface
{
    public function save(Fleet $fleet);
    public function count() : int;
    public function findByUserId(string $userId): ?Fleet;
}