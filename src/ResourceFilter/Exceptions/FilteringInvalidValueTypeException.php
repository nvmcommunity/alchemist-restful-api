<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions;

use Throwable;

class FilteringInvalidValueTypeException extends FilteringException
{
    private string $filteringTitle;
    /**
     * @var string[]
     */
    private array $validValueTypes;

    /**
     * @param string $filteringTitle
     * @param string[] $validValueTypes
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $filteringTitle, array $validValueTypes, $code = 0, Throwable $previous = null)
    {
        $this->filteringTitle = $filteringTitle;
        $this->validValueTypes = $validValueTypes;

        $message = "Filtering [{$filteringTitle}] just only support value type: " . implode(', ', $validValueTypes) . '.';

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getFilteringTitle(): string
    {
        return $this->filteringTitle;
    }

    /**
     * @return array
     */
    public function getValidValueTypes(): array
    {
        return $this->validValueTypes;
    }
}