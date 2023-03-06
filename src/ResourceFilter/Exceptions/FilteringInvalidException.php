<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidException extends FilteringException
{
    public function __construct($filteringTitle, $code = 0, Throwable $previous = null)
    {
        $message = "Filtering {$filteringTitle} is invalid";

        parent::__construct($message, $code, $previous);
    }
}