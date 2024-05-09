<?php

namespace Fulll\App\Shared\Command;

interface CommandHandlerInterface
{
    public function __invoke(CommandInterface $command): mixed;
}