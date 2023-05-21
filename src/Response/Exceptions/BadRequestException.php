<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class BadRequestException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Bad Request", Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}