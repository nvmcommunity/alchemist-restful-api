<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class ForbiddenException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Forbidden", Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}