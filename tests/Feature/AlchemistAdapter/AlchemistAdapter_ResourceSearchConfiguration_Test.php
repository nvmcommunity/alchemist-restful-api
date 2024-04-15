<?php

namespace Feature\AlchemistAdapter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use PHPUnit\Framework\TestCase;

class AlchemistAdapter_ResourceSearchConfiguration_Test extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_in_default_behavior_with_resource_search_must_pass(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSearch::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSearch::class => [
                    'request_params' => [
                        'search_param' => 'search',
                    ]
                ],
            ]);

        $restfulApi = new AlchemistRestfulApi([
            'search' => 'abc'
        ], $adapter);

        $restfulApi->resourceSearch()->defineSearchCondition('title');

        $this->assertSame('title', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('abc', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_search_enable_but_missing_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSearch::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
//              >Missing below config:
//              ResourceSearch::class => [
//                  ...
//              ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_search_enable_but_missing_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSearch::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSearch::class => [
//                      >Missing below config:
//                      'request_params' => [
//                          'filtering_param' => 'filtering',
//                      ],
//                   ]
//                ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_search_enable_but_missing_filtering_param_inside_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSearch::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSearch::class => [
                    'request_params' => [
//                      >Missing below config:
//                      'search_param' => 'search',
                    ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }
}
