<?php
namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;

trait FieldSelectable
{
    /**
     * @var FieldSelector
     */
    private FieldSelector $fieldSelector;

    /**
     * @param array $requestInput
     * @return void
     * @throws AlchemistRestfulApiException
     */
    private function initFieldSelector(array $requestInput): void
    {
        $componentConfig = $this->adapter->componentConfigs();

        if (! isset($componentConfig[FieldSelector::class])) {
            throw new AlchemistRestfulApiException(
                "The `FieldSelector` component is not configured!
                Add below config into componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                FieldSelector::class => [
                    'request_params' => [
                        'fields_param' => 'fields',
                    ]
                ],
                ```
                ", AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT
            );
        }

        if (! isset($componentConfig[FieldSelector::class]['request_params'])) {
            throw new AlchemistRestfulApiException("Missing `request_params` for `FieldSelector` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                FieldSelector::class => [
                    'request_params' => [
                        'fields_param' => 'fields',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT
            );
        }

        if (! isset($componentConfig[FieldSelector::class]['request_params']['fields_param'])) {
            throw new AlchemistRestfulApiException("Missing `fields_param` for `request_params` in `FieldSelector` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                FieldSelector::class => [
                    'request_params' => [
                        'fields_param' => 'fields',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::FIELD_SELECTOR_CONFIGURATION_INCORRECT);
        }

        $fieldsParam = $componentConfig[FieldSelector::class]['request_params']['fields_param'];

        $this->fieldSelector = new FieldSelector($requestInput[$fieldsParam] ?? null);
    }

    /**
     * @return FieldSelector
     */
    public function fieldSelector(): FieldSelector
    {
        return $this->fieldSelector;
    }
}