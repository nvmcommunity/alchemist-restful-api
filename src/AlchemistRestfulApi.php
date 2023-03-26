<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Numerics;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\AlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\ResourceOffsetPaginate;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\ResourceSearchable;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\ResourceSortable;
use stdClass;

class AlchemistRestfulApi
{
    use FieldSelectable,
        ResourceFilterable,
        ResourceOffsetPaginate,
        ResourceSortable,
        ResourceSearchable;

    /**
     * @param array $requestInput
     * @throws AlchemistRestfulApiException
     */
    public function __construct(array $requestInput)
    {
        if ($this->isModuleEnable(FieldSelectable::class)) {
            if (isset($requestInput['fields']) && ! is_string($requestInput['fields'])) {
                throw new AlchemistRestfulApiException('The `fields` request input parameter must be type of string');
            }

            $this->initFieldSelector($requestInput['fields'] ?? '');
        }

        if ($this->isModuleEnable(ResourceFilterable::class)) {
            if (isset($requestInput['filtering']) && ! is_array($requestInput['filtering'])) {
                throw new AlchemistRestfulApiException('The `filtering` request input parameter must be type of array');
            }

            $this->initResourceFilter($requestInput['filtering'] ?? []);
        }

        if ($this->isModuleEnable(ResourceOffsetPaginate::class)) {
            if (isset($requestInput['limit']) && ! Numerics::isIntegerValue($requestInput['limit'])) {
                throw new AlchemistRestfulApiException('The `limit` request input parameter must be type of integer');
            }

            if (isset($requestInput['offset']) && ! Numerics::isIntegerValue($requestInput['offset'])) {
                throw new AlchemistRestfulApiException('The `offset` request input parameter must be type of integer');
            }

            $this->initResourceOffsetPaginator($requestInput['limit'] ?? 0, $requestInput['offset'] ?? 0);
        }

        if ($this->isModuleEnable(ResourceSortable::class)) {
            if (isset($requestInput['sort']) && ! is_string($requestInput['sort'])) {
                throw new AlchemistRestfulApiException('The `sort` request input parameter must be type of string');
            }

            if (isset($requestInput['direction']) && ! is_string($requestInput['direction'])) {
                throw new AlchemistRestfulApiException('The `direction` request input parameter must be type of string');
            }

            $this->initResourceSort($requestInput['sort'] ?? '', $requestInput['direction'] ?? '');
        }

        if ($this->isModuleEnable(ResourceSearchable::class)) {
            if (isset($requestInput['search']) && ! is_string($requestInput['search'])) {
                throw new AlchemistRestfulApiException('The `search` request input parameter must be type of string');
            }

            $this->initResourceSearch($requestInput['search'] ?? '');
        }
    }

    /**
     * @param $errorBag
     * @return ErrorBag
     */
    public function validate(&$errorBag = null): ErrorBag
    {
        $errors = new stdClass();

        $passes = true;

        if ($this->isModuleEnable(FieldSelectable::class)) {
            if (! $this->fieldSelector()->validate($fieldSelectorErrorBag)->passes()) {
                $passes = false;
            }

            $errors->fieldSelector = $fieldSelectorErrorBag;
        }

        if ($this->isModuleEnable(ResourceFilterable::class)) {
            if (! $this->resourceFilter()->validate($resourceFilterErrorBag)->passes()) {
                $passes = false;
            }

            $errors->resourceFilter = $resourceFilterErrorBag;

        }

        if ($this->isModuleEnable(ResourceOffsetPaginate::class)) {
            if (! $this->resourceOffsetPaginator()->validate($resourceOffsetPaginatorErrorBag)->passes()) {
                $passes = false;
            }

            $errors->resourceOffsetPaginator = $resourceOffsetPaginatorErrorBag;
        }

        if ($this->isModuleEnable(ResourceSortable::class)) {
            if (! $this->resourceSort()->validate($resourceSortErrorBag)->passes()) {
                $passes = false;
            }

            $errors->resourceSort = $resourceSortErrorBag;
        }

        return $errorBag = new ErrorBag($passes, $errors);
    }

    /**
     * @param string $className
     * @param array $requestInput
     * @return AlchemistRestfulApi
     * @throws AlchemistRestfulApiException
     */
    public static function for(string $className, array $requestInput): self
    {
        /** @var AlchemistQueryable $className */

        $instance = new self($requestInput);

        if ($instance->isModuleEnable(FieldSelectable::class)) {
            $className::fieldSelector($instance->fieldSelector());
        }

        if ($instance->isModuleEnable(ResourceFilterable::class)) {
            $className::resourceFilter($instance->resourceFilter());
        }

        if ($instance->isModuleEnable(ResourceOffsetPaginate::class)) {
            $className::resourceOffsetPaginator($instance->resourceOffsetPaginator());
        }

        if ($instance->isModuleEnable(ResourceSortable::class)) {
            $className::resourceSort($instance->resourceSort());
        }

        if ($instance->isModuleEnable(ResourceSearchable::class)) {
            $className::resourceSearch($instance->resourceSearch());
        }

        return $instance;
    }

    /**
     * @param string $module
     * @return bool
     */
    private function isModuleEnable(string $module): bool
    {
        return array_key_exists($module, class_uses($this));
    }
}