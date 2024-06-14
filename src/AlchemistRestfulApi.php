<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\AlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\CompoundErrors;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\ResourceOffsetPaginate;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\ResourceSearchable;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\ResourceSortable;
use Nvmcommunity\Alchemist\RestfulApi\Response\Compose\ResourceResponsible;

class AlchemistRestfulApi
{
    use FieldSelectable,
        ResourceFilterable,
        ResourceOffsetPaginate,
        ResourceSortable,
        ResourceSearchable,
        ResourceResponsible;

    protected AlchemistAdapter $adapter;

    /**
     * @param array $requestInput
     * @param AlchemistAdapter|null $adapter
     * @throws AlchemistRestfulApiException
     */
    public function __construct(array $requestInput, ?AlchemistAdapter $adapter = null)
    {
        $adapter = $adapter ?? new AlchemistAdapter;

        $this->setAdapter($adapter);

        if ($this->isComponentUses(FieldSelector::class)) {
            $this->initFieldSelector($requestInput);
        }

        if ($this->isComponentUses(ResourceFilter::class)) {
            $this->initResourceFilter($requestInput);
        }

        if ($this->isComponentUses(ResourceOffsetPaginator::class)) {
            $this->initResourceOffsetPaginator($requestInput);
        }

        if ($this->isComponentUses(ResourceSearch::class)) {
            $this->initResourceSearch($requestInput);
        }

        if ($this->isComponentUses(ResourceSort::class)) {
            $this->initResourceSort($requestInput);
        }

        $this->initResponseCompose($this);
    }

    /**
     * @param $apiClass
     * @param array $requestInput
     * @param AlchemistAdapter|null $adapter
     * @return AlchemistRestfulApi
     * @throws AlchemistRestfulApiException
     */
    public static function for($apiClass, array $requestInput, ?AlchemistAdapter $adapter = null): self
    {
        /** @var AlchemistQueryable $apiClass */

        if (! is_object($apiClass)) {
            $apiClass = new $apiClass;
        }

        if ($adapter !== null) {
            $adapter = $apiClass->getAdapter();
        }

        $instance = new self($requestInput, $adapter);

        if ($instance->isComponentUses(FieldSelector::class)) {
            $apiClass->fieldSelector($instance->fieldSelector());
        }

        if ($instance->isComponentUses(ResourceFilter::class)) {
            $apiClass->resourceFilter($instance->resourceFilter());
        }

        if ($instance->isComponentUses(ResourceOffsetPaginator::class)) {
            $apiClass->resourceOffsetPaginator($instance->resourceOffsetPaginator());
        }

        if ($instance->isComponentUses(ResourceSort::class)) {
            $apiClass->resourceSort($instance->resourceSort());
        }

        if ($instance->isComponentUses(ResourceSearch::class)) {
            $apiClass->resourceSearch($instance->resourceSearch());
        }

        return $instance;
    }

    /**
     * @param AlchemistAdapter $adapter
     */
    public function setAdapter(AlchemistAdapter $adapter): void
    {
        $this->adapter = $adapter;

        $this->adapter->for($this);
    }

    /**
     * @return AlchemistAdapter
     */
    public function getAdapter(): AlchemistAdapter
    {
        return $this->adapter;
    }

    /**
     * @param ErrorBag|null $errorBag
     * @return ErrorBag
     */
    public function validate(?ErrorBag &$errorBag = null): ErrorBag
    {
        $errors = new CompoundErrors;

        $passes = true;

        if ($this->isComponentUses(FieldSelector::class)
            && ! $this->fieldSelector()->validate($fieldSelectorErrorBag)->passes()
        ) {
            $passes = false;

            $errors->fieldSelector = $fieldSelectorErrorBag;
        }

        if ($this->isComponentUses(ResourceFilter::class)
            && ! $this->resourceFilter()->validate($resourceFilterErrorBag)->passes()
        ) {
            $passes = false;

            $errors->resourceFilter = $resourceFilterErrorBag;
        }

        if ($this->isComponentUses(ResourceOffsetPaginator::class)
            && ! $this->resourceOffsetPaginator()->validate($resourceOffsetPaginatorErrorBag)->passes()
        ) {
            $passes = false;

            $errors->resourceOffsetPaginator = $resourceOffsetPaginatorErrorBag;
        }

        if ($this->isComponentUses(ResourceSearch::class)
            && ! $this->resourceSearch()->validate($resourceSearchErrorBag)->passes()
        ) {
            $passes = false;

            $errors->resourceSearch = $resourceSearchErrorBag;
        }

        if ($this->isComponentUses(ResourceSort::class)
            && ! $this->resourceSort()->validate($resourceSortErrorBag)->passes()
        ) {
            $passes = false;

            $errors->resourceSort = $resourceSortErrorBag;
        }

        $errorBag = new ErrorBag($passes, $errors);

        if (method_exists($this->adapter, 'errorHandler')) {
            $this->adapter->errorHandler($errorBag);
        }

        return $errorBag;
    }

    /**
     * @param string $componentClass
     * @return bool
     */
    public function isComponentUses(string $componentClass): bool
    {
        return in_array($componentClass, $this->adapter->componentUses());
    }

    /**
     * @param string $componentClass
     * @return array
     */
    public function getComponentRequestParams(string $componentClass): array
    {
        return $this->adapter->componentConfigs()[$componentClass]['request_params'] ?? [];
    }
}