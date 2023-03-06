<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidValueSyntaxException extends FilteringException
{
    public function __construct(string $filteringTitle, string $validSyntax, string $example, $code = 0, Throwable $previous = null)
    {
        $message = "{$filteringTitle} is invalid value syntax. Syntax must be is {$validSyntax}. Example: {$example}";

        parent::__construct($message, $code, $previous);
    }
}