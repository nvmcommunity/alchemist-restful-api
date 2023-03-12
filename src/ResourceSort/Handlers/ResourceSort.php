<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Notifications\ResourceSortValidationNotification;
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
    private string $defaultSortField;
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
     */
    public function defineDefaultDirection(string $direction): self
    {
        $this->defaultSortDirection = $direction;

        return $this;
    }

    /**
     * @param $notification
     * @return ResourceSortValidationNotification
     */
    public function validate(&$notification = null): ResourceSortValidationNotification
    {
        $passes = true;

        $invalidDirection = false;

        if (! in_array($this->direction, ['asc', 'desc'], true)) {
            $passes = false;
            $invalidDirection = true;
        }

        if (! in_array($this->direction, ['asc', 'desc'], true)) {
            $passes = false;
            $invalidDirection = true;
        }

        return $notification = new ResourceSortValidationNotification($passes, $invalidDirection);
    }

    /**
     * @return ResourceSortObject
     */
    public function resourceSort(): ResourceSortObject
    {
        return new ResourceSortObject(
            $this->sortField ?: $this->defaultSortField,
            $this->direction ?: $this->defaultSortDirection
        );
    }
}