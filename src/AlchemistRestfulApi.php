<?php

namespace Nvmcommunity\Alchemist\RestfulApi;

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Exceptions\FieldSelectorSyntaxErrorException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\FieldSelectable;

class AlchemistRestfulApi
{
    use FieldSelectable;

    /**
     * @param array $requestInput
     * @throws FieldSelectorSyntaxErrorException
     */
    public function __construct(array $requestInput)
    {
        $this->initFieldSelector($requestInput['fields'] ?? '');
    }
}