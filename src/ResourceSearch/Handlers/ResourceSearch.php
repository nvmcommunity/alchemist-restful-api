<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Notifications\ResourceSearchErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Objects\ResourceSearchObject;

class ResourceSearch
{
    /**
     * @var mixed
     */
    private $originalInput;

    /**
     * @var string
     */
    private string $searchValue;
    /**
     * @var string
     */
    private string $searchCondition = '';

    /**
     * @param mixed $searchValue
     */
    public function __construct($searchValue)
    {
        $this->originalInput = $searchValue;

        $this->searchValue = is_string($searchValue) ? $searchValue : '';
    }

    /**
     * @param string $condition
     * @return ResourceSearch
     */
    public function defineSearchCondition(string $condition): self
    {
        $this->searchCondition = $condition;

        return $this;
    }

    /**
     * @return ResourceSearchObject
     */
    public function search(): ResourceSearchObject
    {
        return new ResourceSearchObject($this->searchValue, $this->searchCondition);
    }

    /**
     * @param $notification
     * @return ResourceSearchErrorBag
     */
    public function validate(&$notification = null): ResourceSearchErrorBag
    {
        if (! is_null($this->originalInput) && ! is_string($this->originalInput)) {
            return $notification = new ResourceSearchErrorBag(false, true);
        }

        return $notification = new ResourceSearchErrorBag(true);
    }
}