<?php

namespace Feature\AlchemistAdapter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;
use PHPUnit\Framework\TestCase;

class AlchemistAdapter_ResourceSortConfiguration_Test extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_in_default_behavior_with_resource_sort_must_pass(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSort::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSort::class => [
                    'request_params' => [
                        'sort_param' => 'sort',
                        'direction_param' => 'direction',
                    ]
                ],
            ]);

        $restfulApi = new AlchemistRestfulApi([
            'sort' => 'title',
            'direction' => 'asc',
        ], $adapter);

        $restfulApi->resourceSort()->defineSortableFields(['title']);

        $this->assertSame('title', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_sort_enable_but_missing_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSort::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
//              >Missing below config:
//              ResourceSort::class => [
//                  ...
//              ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SORT_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_sort_enable_but_missing_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSort::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSort::class => [
//                      >Missing below config:
//                      'request_params' => [
//                          'filtering_param' => 'filtering',
//                      ],
//                   ]
//                ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SORT_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_resource_sort_enable_but_missing_sort_and_direction_param_inside_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                ResourceSort::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                ResourceSort::class => [
                    'request_params' => [
//                      >Missing below config:
//                      'sort_param' => 'sort',
//                      'direction_param' => 'direction',
                    ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::RESOURCE_SORT_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }
}
