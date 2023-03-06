<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\ResourceFilterable;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Exceptions\FieldSelectorSyntaxErrorException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;

class AlchemistRestfulApi
{
    use FieldSelectable, ResourceFilterable;

    /**
     * @param array $requestInput
     * @throws FieldSelectorSyntaxErrorException
     * @throws AlchemistRestfulApiException
     */
    public function __construct(array $requestInput)
    {
        if (isset($requestInput['fields']) && ! is_string($requestInput['fields'])) {
            throw new AlchemistRestfulApiException('The `fields` parameter must be type of string');
        }
        if (isset($requestInput['filtering']) && ! is_array($requestInput['filtering'])) {
            throw new AlchemistRestfulApiException('The `filtering` parameter must be type of array');
        }

        $this->initFieldSelector($requestInput['fields'] ?? '');

        $this->initResourceFilter($requestInput['filtering'] ?? []);
    }
}