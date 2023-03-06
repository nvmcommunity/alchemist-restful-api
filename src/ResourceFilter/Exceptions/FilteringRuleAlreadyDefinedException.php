<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringRuleAlreadyDefinedException extends FilteringException
{
    public function __construct(string $filteringDotName, $code = 0, Throwable $previous = null)
    {
        $message = "Filtering rule for {$filteringDotName} is already defined";

        parent::__construct($message, $code, $previous);
    }
}