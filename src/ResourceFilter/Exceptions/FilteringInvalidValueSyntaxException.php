<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidValueSyntaxException extends FilteringException
{
    private string $filteringTitle;
    private string $validSyntax;
    private string $filteringOperator;

    public function __construct(string $filteringOperator, string $filteringTitle, string $validSyntax, string $example, $code = 0, Throwable $previous = null)
    {
        $this->filteringTitle = $filteringTitle;
        $this->validSyntax = $validSyntax;
        $this->filteringOperator = $filteringOperator;

        $message = "{$filteringTitle} is invalid value syntax. Syntax must be is {$validSyntax}. Example: {$example}";

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getFilteringOperator(): string
    {
        return $this->filteringOperator;
    }

    /**
     * @return string
     */
    public function getFilteringTitle(): string
    {
        return $this->filteringTitle;
    }

    /**
     * @return string
     */
    public function getValidSyntax(): string
    {
        return $this->validSyntax;
    }
}