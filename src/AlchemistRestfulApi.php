<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Strings;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\AlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\StatefulAlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\CompoundErrors;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\Common\Objects\BaseAlchemistComponent;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\ResourceOffsetPaginate;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\ResourceSearchable;
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

        foreach ($adapter->componentUses() as $componentClass) {
            $this->{"init".$this->componentName($componentClass)}($requestInput);
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

        if ($adapter !== null) {
            $adapter = $apiClass->getAdapter();
        }

        $instance = new self($requestInput, $adapter);

        foreach ($instance->adapter->componentUses() as $componentClass) {
            if (! $instance->isComponentUses($componentClass)) {
                continue;
            }

            $componentPropertyName = $instance->componentPropertyName($componentClass);

            $apiClass->{$componentPropertyName}($instance->{$componentPropertyName}());
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

        foreach ($this->adapter->componentUses() as $componentClass) {
            /** @var BaseAlchemistComponent $componentHandler */
            $componentHandler = $this->{$this->componentPropertyName($componentClass)};

            if (! $componentHandler->validate($componentErrorBag)->passes()
            ) {
                $passes = false;

                $errors->{$this->componentPropertyName($componentClass)} = $componentErrorBag;
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