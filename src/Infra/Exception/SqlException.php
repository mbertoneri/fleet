<?php

namespace Fulll\Infra\Exception;

final class SqlException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
