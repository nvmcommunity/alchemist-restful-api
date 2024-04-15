<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;

trait ResourceFilterable
{
    /**
     * @var ResourceFilter
     */
    private ResourceFilter $resourceFilter;

    /**
     * @param array $requestInput
     * @return void
     * @throws AlchemistRestfulApiException
     */
    private function initResourceFilter(array $requestInput): void
    {
        $componentConfig = $this->adapter->componentConfigs();

        if (! isset($componentConfig[ResourceFilter::class])) {
            throw new AlchemistRestfulApiException("The `ResourceFilter` component is not configured!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceFilter::class => [
                    'request_params' => [
                        'filtering_param' => 'filter',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceFilter::class]['request_params'])) {
            throw new AlchemistRestfulApiException("Missing `request_params` configuration for `ResourceFilter` component!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceFilter::class => [
                    'request_params' => [
                        'filtering_param' => 'filter',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceFilter::class]['request_params']['filtering_param'])) {
            throw new AlchemistRestfulApiException("Missing `filtering_param` configuration for request_params in `ResourceFilter` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceFilter::class => [
                    'request_params' => [
                        'filtering_param' => 'filter',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_FILTER_CONFIGURATION_INCORRECT);
        }

        $filteringParam = $componentConfig[ResourceFilter::class]['request_params']['filtering_param'];

        $this->resourceFilter = new ResourceFilter($requestInput[$filteringParam] ?? []);
    }

    /**
     * @return ResourceFilter
     */
    public function resourceFilter(): ResourceFilter
    {
        return $this->resourceFilter;
    }
}