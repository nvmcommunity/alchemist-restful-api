<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Throwable;

class MethodNotAllowedException extends \BadFunctionCallException implements ApplicationExceptionInterface
{
    public function __construct($message = "Method Not Allowed", Throwable $previous = null)
    {
        parent::__construct($message, 405, $previous);
    }
}