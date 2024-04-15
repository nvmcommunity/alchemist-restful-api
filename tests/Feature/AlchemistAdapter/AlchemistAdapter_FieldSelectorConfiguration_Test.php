<?php

namespace Feature\AlchemistAdapter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use PHPUnit\Framework\TestCase;

class AlchemistAdapter_FieldSelectorConfiguration_Test extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_in_default_behavior_with_field_selector_must_pass(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                FieldSelector::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                FieldSelector::class => [
                    'request_params' => [
                        'fields_param' => 'fields',
                    ]
                ],
            ]);

        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,order_date'
        ], $adapter);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
            ]);

        $this->assertSame(['id', 'order_date'], $restfulApi->fieldSelector()->flatFields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_field_selector_enable_but_missing_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                FieldSelector::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
//              >Missing below config:
//              FieldSelector::class => [
//                  ...
//              ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_field_selector_enable_but_missing_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                FieldSelector::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                FieldSelector::class => [
//                      >Missing below config:
//                      'request_params' => [
//                          'fields_param' => 'fields',
//                      ],
//                   ]
//                ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_adapter_with_field_selector_enable_but_missing_fields_param_inside_request_params_of_configuration_must_throw_exception(): void
    {
        $adapter = $this->createMock(AlchemistAdapter::class);
        $adapter->method('componentUses')
            ->willReturn([
                FieldSelector::class,
            ]);

        $adapter->method('componentConfigs')
            ->willReturn([
                FieldSelector::class => [
                    'request_params' => [
//                      >Missing below config:
//                      'fields_param' => 'fields',
                    ],
                ],
            ]);

        $this->expectExceptionCode(AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT);

        new AlchemistRestfulApi([], $adapter);
    }
}
