<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class ConflictException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Conflict", Throwable $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }
}