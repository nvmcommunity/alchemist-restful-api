<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class NotFoundException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Not Found", Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}