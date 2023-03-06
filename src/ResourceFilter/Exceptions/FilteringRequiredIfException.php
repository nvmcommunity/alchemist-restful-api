<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringRequiredIfException extends FilteringException
{
    public function __construct($requiredFiltering, $ifFilteringNameWithOperator, $ifFilteringCondition, $code = 0, Throwable $previous = null)
    {
        $message = "Filtering {$requiredFiltering} must be required if filtering {$ifFilteringNameWithOperator} has value {$ifFilteringCondition}";

        parent::__construct($message, $code, $previous);
    }
}