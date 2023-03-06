<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidRuleException extends FilteringException
{
    public function __construct(string $filteringTitle, $code = 0, Throwable $previous = null)
    {
        $message = "Can not found filtering rule from filtering {$filteringTitle}";

        parent::__construct($message, $code, $previous);
    }
}