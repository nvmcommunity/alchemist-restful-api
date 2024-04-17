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
     * @var string
     */
    private string $sortField;
    /**
     * @var string
     */
    private string $direction;
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
     * @param string $sort
     * @param string $direction
     */
    public function __construct(string $sort, string $direction)
    {
        $this->sortField = $sort;
        $this->direction = $direction;
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

        if (! empty($this->direction) && ! in_array($this->direction, ['asc', 'desc'], true)) {
            $passes = false;
            $invalidDirection = true;
        }

        if (! empty($this->sortField) && (empty($this->sortableFields) || ! in_array($this->sortField, $this->sortableFields, true))) {
            $passes = false;
            $invalidSortField = true;
        }

        return $notification = new ResourceSortErrorBag($passes, $invalidDirection, $invalidSortField);
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