<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringRequiredWithException extends FilteringException
{
    public function __construct($requiredFiltering, $withPresentFiltering, $code = 0, Throwable $previous = null)
    {
        $message = "Filtering {$requiredFiltering} must be required with filtering {$withPresentFiltering} if it present";

        parent::__construct($message, $code, $previous);
    }
}