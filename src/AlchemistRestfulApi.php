<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Strings;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\AlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\StatefulAlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\CompoundErrors;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
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

        $this->linkAdapter($adapter);

        foreach ($adapter->componentUses() as $componentClass) {
            switch ($componentClass) {
                case FieldSelector::class:
                    $this->initFieldSelector($requestInput);
                    break;
                case ResourceFilter::class:
                    $this->initResourceFilter($requestInput);
                    break;
                case ResourceOffsetPaginator::class:
                    $this->initResourceOffsetPaginator($requestInput);
                    break;
                case ResourceSort::class:
                    $this->initResourceSort($requestInput);
                    break;
                case ResourceSearch::class:
                    $this->initResourceSearch($requestInput);
                    break;
            }
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
        /** @var AlchemistQueryable|StatefulAlchemistQueryable $apiClass */

        if (! is_object($apiClass)) {
            $apiClass = new $apiClass;
        }

        $hasValidApiInstance = $apiClass instanceof AlchemistQueryable
            || $apiClass instanceof StatefulAlchemistQueryable;

        if (! $hasValidApiInstance) {
            throw new \RuntimeException("Api Class must be instance of AlchemistQueryable or StatefulAlchemistQueryable");
        }

        if ($adapter === null) {
            if ($apiClass instanceof AlchemistQueryable) {
                $adapter = $apiClass::getAdapter();
            } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                $adapter = $apiClass->getAdapter();
            }
        }

        $instance = new self($requestInput, $adapter);

        foreach ($instance->adapter->componentUses() as $componentClass) {
            if (! $instance->isComponentUses($componentClass)) {
                continue;
            }

            switch ($componentClass) {
                case FieldSelector::class:
                    if ($apiClass instanceof AlchemistQueryable) {
                        $apiClass::fieldSelector($instance->fieldSelector());
                    } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                        $apiClass->fieldSelector($instance->fieldSelector());
                    }
                    break;
                case ResourceFilter::class:
                    if ($apiClass instanceof AlchemistQueryable) {
                        $apiClass::resourceFilter($instance->resourceFilter());
                    } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                        $apiClass->resourceFilter($instance->resourceFilter());
                    }
                    break;
                case ResourceOffsetPaginator::class:
                    if ($apiClass instanceof AlchemistQueryable) {
                        $apiClass::resourceOffsetPaginator($instance->resourceOffsetPaginator());
                    } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                        $apiClass->resourceOffsetPaginator($instance->resourceOffsetPaginator());
                    }
                    break;
                case ResourceSort::class:
                    if ($apiClass instanceof AlchemistQueryable) {
                        $apiClass::resourceSort($instance->resourceSort());
                    } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                        $apiClass->resourceSort($instance->resourceSort());
                    }
                    break;
                case ResourceSearch::class:
                    if ($apiClass instanceof AlchemistQueryable) {
                        $apiClass::resourceSearch($instance->resourceSearch());
                    } elseif ($apiClass instanceof StatefulAlchemistQueryable) {
                        $apiClass->resourceSearch($instance->resourceSearch());
                    }
                    break;
                default:
                    throw new \RuntimeException("Missing handle for component '{$componentClass}'");
            }
        }

        return $instance;
    }

    /**
     * @deprecated Use linkAdapter() instead.
     *
     * @param AlchemistAdapter $adapter
     */
    public function setAdapter(AlchemistAdapter $adapter): void
    {
        $this->linkAdapter($adapter);
    }

    /**
     * @param AlchemistAdapter $adapter
     */
    public function linkAdapter(AlchemistAdapter $adapter): void
    {
        $this->adapter = $adapter;
        $this->adapter->setAlchemistRestfulApi($this);
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

        foreach ($this->adapter->componentUses() as $componentClass) {
            switch ($componentClass) {
                case FieldSelector::class:
                    if (! $this->fieldSelector->validate($componentErrorBag)->passes()) {
                        $passes = false;
                        $errors->fieldSelector = $componentErrorBag;
                    }
                    break;
                case ResourceFilter::class:
                    if (! $this->resourceFilter->validate($componentErrorBag)->passes()) {
                        $passes = false;
                        $errors->resourceFilter = $componentErrorBag;
                    }
                    break;
                case ResourceOffsetPaginator::class:
                    if (! $this->resourceOffsetPaginator->validate($componentErrorBag)->passes()) {
                        $passes = false;
                        $errors->resourceOffsetPaginator = $componentErrorBag;
                    }
                    break;
                case ResourceSort::class:
                    if (! $this->resourceSort->validate($componentErrorBag)->passes()) {
                        $passes = false;
                        $errors->resourceSort = $componentErrorBag;
                    }
                    break;
                case ResourceSearch::class:
                    if (! $this->resourceSearch->validate($componentErrorBag)->passes()) {
                        $passes = false;
                        $errors->resourceSearch = $componentErrorBag;
                    }
                    break;
                default:
                    throw new \RuntimeException("Missing validate for component '{$componentClass}'");
            }
        }

        $errorBag = new ErrorBag($passes, $errors);

        if (method_exists($this->adapter, 'errorHandler')) {
            $this->adapter->errorHandler($errorBag);
        }

        return $errorBag;
    }

    /**
     * @param string $componentClass
     * @return string
     */
    public function componentName(string $componentClass): string
    {
        return ucfirst(Strings::end($componentClass, '\\'));
    }

    /**
     * @param string $componentClass
     * @return string
     */
    public function componentPropertyName(string $componentClass): string
    {
        return lcfirst(Strings::end($componentClass, '\\'));
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