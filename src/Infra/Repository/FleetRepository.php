<?php

namespace Fulll\Infra\Repository;

use Fulll\Domain\Domain\FleetRepositoryInterface;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Model\Fleet;

final class FleetRepository implements FleetRepositoryInterface
{

    /** @var array Fleet */
    private array $fleets;

    public function __construct(
    ){
        $this->fleets = [];
    }

    public function save(Fleet $fleet)
    {
        $this->fleets[$fleet->getId()] = $fleet;
    }

    public function findById(string $id): Fleet
    {
        foreach ($this->fleets as $fleet) {
            if ($fleet->getId() === $id) {
                return $fleet;
            }
        }

        throw new FleetNotFoundException($id);
    }

    public function count(): int
    {
        return \count($this->fleets);
    }
}