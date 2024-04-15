<?php

namespace Feature\AlchemistAdapter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use PHPUnit\Framework\TestCase;

class AlchemistAdapter_ResourceOffsetPaginator_Test extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_in_default_behavior_with_resource_offset_paginator_must_pass(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceOffsetPaginator::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceOffsetPaginator::class => [
                    'request_params' => [
                        'limit_param' => 'limit',
                        'offset_param' => 'offset',
                    ]
                ],
            ]);

        $restfulApi = new AlchemistRestfulApi([
            'limit' => 10,
            'offset' => 0,
        ], $adapter);

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_Adapter_with_resource_offset_paginator_enable_but_missing_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceOffsetPaginator::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
//              >Missing below config:
//              ResourceOffsetPaginator::class => [
//                  ...
//              ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_offset_paginator_enable_but_missing_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceOffsetPaginator::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceOffsetPaginator::class => [
//                      >Missing below config:
//                      'request_params' => [
//                          'fields_param' => 'fields',
//                      ],
//                   ]
//                ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_offset_paginator_enable_but_missing_offset_and_limit_param_inside_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceOffsetPaginator::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceOffsetPaginator::class => [
                    'request_params' => [
//                      >Missing below config:
//                      'limit_param' => 'fields',
//                      'offset_param' => 'fields',
                    ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }
}
