<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;

trait ResourceSearchable
{
    /**
     * @var ResourceSearch
     */
    private ResourceSearch $resourceSearch;

    /**
     * @param array $requestInput
     * @return void
     * @throws AlchemistRestfulApiException
     */
    private function initResourceSearch(array $requestInput): void
    {
        $componentConfig = $this->adapter->componentConfigs();

        if (! isset($componentConfig[ResourceSearch::class])) {
            throw new AlchemistRestfulApiException("The `ResourceSearch` component is not configured!
               Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceSearch::class => [
                    'request_params' => [
                        'search_param' => 'search',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceSearch::class]['request_params'])) {
            throw new AlchemistRestfulApiException("Missing `request_params` configuration for `ResourceSearch` component!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceSearch::class => [
                    'request_params' => [
                        'search_param' => 'search',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceSearch::class]['request_params']['search_param'])) {
            throw new AlchemistRestfulApiException("Missing `search_param` configuration for request_params in `ResourceSearch` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceSearch::class => [
                    'request_params' => [
                        'search_param' => 'search',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_SEARCH_CONFIGURATION_INCORRECT);
        }

        $searchParam = $componentConfig[ResourceSearch::class]['request_params']['search_param'];

        $this->resourceSearch = new ResourceSearch($requestInput[$searchParam] ?? null);
    }

    /**
     * @return ResourceSearch
     */
    public function resourceSearch(): ResourceSearch
    {
        return $this->resourceSearch;
    }
}