<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class UnauthorizedException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Unauthorized", Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}