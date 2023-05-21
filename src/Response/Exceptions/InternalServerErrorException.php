<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class InternalServerErrorException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Internal Server Error", Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}