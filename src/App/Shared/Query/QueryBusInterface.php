<?php

namespace Fulll\App\Shared\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
