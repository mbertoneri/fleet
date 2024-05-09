<?php

namespace Fulll\Domain\Domain;

use Fulll\Domain\Model\Fleet;

interface FleetRepositoryInterface
{
    public function save(Fleet $fleet);
    public function count() : int;
    public function findById(string $id): Fleet;
}