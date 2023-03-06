<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringRequiredException extends FilteringException
{
    public function __construct(string $filteringTitle, $code = 0, Throwable $previous = null)
    {
        $message = "{$filteringTitle} must be required";

        parent::__construct($message, $code, $previous);
    }
}