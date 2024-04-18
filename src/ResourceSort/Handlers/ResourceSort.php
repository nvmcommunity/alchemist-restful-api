<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Notifications\ResourceSortErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Objects\ResourceSortObject;

/**
 *
 */
class ResourceSort
{
    /**
     * @var mixed
     */
    private $originalInput;
    /**
     * @var string
     */
    private string $sortField;
    /**
     * @var string
     */
    private string $direction;
    /**
     * @var string
     */
    private string $sortFieldParam;
    /**
     * @var string
     */
    private string $directionParam;
    /**
     * @var string[]
     */
    private array $sortableFields;
    /**
     * @var string
     */
    private string $defaultSortField = '';
    /**
     * @var string
     */
    private string $defaultSortDirection = 'asc';

    /**
     * @param string $sortParam
     * @param string $directionParam
     * @param array $input
     */
    public function __construct(string $sortParam, string $directionParam, array $input)
    {
        $this->sortFieldParam = $sortParam;
        $this->directionParam = $directionParam;
        $this->originalInput = $input;

        $this->sortField = is_string($input[$sortParam]) ? $input[$sortParam] : '';
        $this->direction = is_string($input[$directionParam]) ? $input[$directionParam] : '';
    }

    /**
     * @param string[] $sortableFields
     * @return ResourceSort
     */
    public function defineSortableFields(array $sortableFields): self
    {
        $this->sortableFields = $sortableFields;

        return $this;
    }

    /**
     * @param string $sortField
     * @return ResourceSort
     */
    public function defineDefaultSort(string $sortField): self
    {
        $this->defaultSortField = $sortField;

        return $this;
    }

    /**
     * @param string $direction
     * @return ResourceSort
     * @throws AlchemistRestfulApiException
     */
    public function defineDefaultDirection(string $direction): self
    {
        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new AlchemistRestfulApiException('Invalid default sort direction, it must be either `asc` or `desc`.');
        }

        $this->defaultSortDirection = $direction;

        return $this;
    }

    /**
     * @param $notification
     * @return ResourceSortErrorBag
     */
    public function validate(&$notification = null): ResourceSortErrorBag
    {
        $passes = true;

        $invalidDirection = false;
        $invalidSortField = false;
        $invalidInputTypes = [];

        if (! is_null($this->originalInput[$this->sortFieldParam]) && ! is_string($this->originalInput[$this->sortFieldParam])) {
            $invalidInputTypes[] = $this->sortFieldParam;
        }

        if (! is_null($this->originalInput[$this->directionParam]) && ! is_string($this->originalInput[$this->directionParam])) {
            $invalidInputTypes[] = $this->directionParam;
        }

        if (! empty($invalidInputTypes)) {
            $passes = false;
        }

        if (! empty($this->direction) && ! in_array($this->direction, ['asc', 'desc'], true)) {
            $passes = false;
            $invalidDirection = true;
        }

        if (! empty($this->sortField) && (empty($this->sortableFields) || ! in_array($this->sortField, $this->sortableFields, true))) {
            $passes = false;
            $invalidSortField = true;
        }

        return $notification = new ResourceSortErrorBag($passes, $invalidDirection, $invalidSortField, $invalidInputTypes);
    }

    /**
     * @return ResourceSortObject
     */
    public function sort(): ResourceSortObject
    {
        return new ResourceSortObject(
            $this->sortField ?: $this->defaultSortField,
            $this->direction ?: $this->defaultSortDirection
        );
    }
}