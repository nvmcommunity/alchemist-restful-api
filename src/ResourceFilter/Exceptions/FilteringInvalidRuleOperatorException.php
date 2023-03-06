<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidRuleOperatorException extends FilteringException
{
    public function __construct(string $filteringDotName, string $filteringOperator, $code = 0, Throwable $previous = null)
    {
        $message = "Can not found filtering rule with operator from filtering {$filteringDotName}:{$filteringOperator}";

        parent::__construct($message, $code, $previous);
    }
}