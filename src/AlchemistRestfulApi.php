<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Numerics;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Exceptions\FieldSelectorSyntaxErrorException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\ResourceOffsetPaginate;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\ResourceSearchable;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\ResourceSortable;

class AlchemistRestfulApi
{
    use FieldSelectable,
        ResourceFilterable,
        ResourceOffsetPaginate,
        ResourceSortable,
        ResourceSearchable;

    /**
     * @param array $requestInput
     * @throws FieldSelectorSyntaxErrorException
     * @throws AlchemistRestfulApiException
     */
    public function __construct(array $requestInput)
    {
        if (isset($requestInput['fields']) && ! is_string($requestInput['fields'])) {
            throw new AlchemistRestfulApiException('The `fields` request input parameter must be type of string');
        }
        if (isset($requestInput['filtering']) && ! is_array($requestInput['filtering'])) {
            throw new AlchemistRestfulApiException('The `filtering` request input parameter must be type of array');
        }
        if (isset($requestInput['limit']) && ! Numerics::isIntegerValue($requestInput['limit'])) {
            throw new AlchemistRestfulApiException('The `limit` request input parameter must be type of integer');
        }
        if (isset($requestInput['offset']) && ! Numerics::isIntegerValue($requestInput['offset'])) {
            throw new AlchemistRestfulApiException('The `offset` request input parameter must be type of integer');
        }
        if (isset($requestInput['sort']) && ! is_string($requestInput['sort'])) {
            throw new AlchemistRestfulApiException('The `sort` request input parameter must be type of string');
        }
        if (isset($requestInput['direction']) && ! is_string($requestInput['direction'])) {
            throw new AlchemistRestfulApiException('The `direction` request input parameter must be type of string');
        }
        if (isset($requestInput['search']) && ! is_string($requestInput['search'])) {
            throw new AlchemistRestfulApiException('The `search` request input parameter must be type of string');
        }

        $this->initFieldSelector($requestInput['fields'] ?? '');

        $this->initResourceFilter($requestInput['filtering'] ?? []);

        $this->initResourceOffsetPaginator($requestInput['limit'] ?? 0, $requestInput['offset'] ?? 0);

        $this->initResourceSort($requestInput['sort'] ?? '', $requestInput['direction'] ?? '');

        $this->initResourceSearch($requestInput['search'] ?? '');
    }
}