<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidValueException extends FilteringException
{
    public function __construct(string $filteringTitle, array $validValues, $code = 0, Throwable $previous = null)
    {
        $message = "Filtering {$filteringTitle} just only support value: " . implode(', ', $validValues) . '.';

        parent::__construct($message, $code, $previous);
    }
}