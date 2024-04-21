<?php

namespace Feature\AlchemistAdapter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use PHPUnit\Framework\TestCase;

class AlchemistAdapter_ResourceFilterConfiguration_Test extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_in_default_behavior_with_resource_filter_must_pass(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceFilter::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceFilter::class => [
                    'request_params' => [
                        'filtering_param' => 'filtering',
                    ]
                ],
            ]);

        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'order_date:lte' => '2023-02-26',
                'product_name:contains' => 'clothes hanger'
            ]
        ], $adapter);

        $restfulApi->resourceFilter()
            ->defineFilteringRules([
                FilteringRules::String('product_name', ['eq', 'contains']),
                FilteringRules::Date('order_date', ['eq', 'lte', 'gte'], ['Y-m-d']),

            ]);

        $this->assertTrue($restfulApi->resourceFilter()->hasFiltering('product_name'));
        $this->assertTrue($restfulApi->resourceFilter()->hasFiltering('order_date'));

        $this->assertNotTrue($restfulApi->resourceFilter()->hasFiltering('abc'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_filter_enable_but_missing_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceFilter::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
//              >Missing below config:
//              ResourceFilter::class => [
//                  ...
//              ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_filter_enable_but_missing_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceFilter::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceFilter::class => [
//                      >Missing below config:
//                      'request_params' => [
//                          'filtering_param' => 'filtering',
//                      ],
//                   ]
//                ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_filter_enable_but_missing_filtering_param_inside_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceFilter::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceFilter::class => [
                    'request_params' => [
//                      >Missing below config:
//                      'filtering_param' => 'filtering',
                    ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }
}
