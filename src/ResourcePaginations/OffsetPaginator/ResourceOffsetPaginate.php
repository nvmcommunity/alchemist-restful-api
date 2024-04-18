<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator;

use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Numerics;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;

trait ResourceOffsetPaginate
{
    /**
     * @var ResourceOffsetPaginator
     */
    private ResourceOffsetPaginator $resourceOffsetPaginator;

    /**
     * @param array $requestInput
     * @return void
     * @throws AlchemistRestfulApiException
     */
    private function initResourceOffsetPaginator(array $requestInput): void
    {
        $componentConfig = $this->adapter->componentConfigs();

        if (! isset($componentConfig[ResourceOffsetPaginator::class])) {
            throw new AlchemistRestfulApiException("The `ResourceOffsetPaginator` component is not configured!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceOffsetPaginator::class => [
                    'request_params' => [
                        'limit_param' => 'limit',
                        'offset_param' => 'offset',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceOffsetPaginator::class]['request_params'])) {
            throw new AlchemistRestfulApiException("Missing `request_params` configuration for `ResourceOffsetPaginator` component!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceOffsetPaginator::class => [
                    'request_params' => [
                        'limit_param' => 'limit',
                        'offset_param' => 'offset',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceOffsetPaginator::class]['request_params']['limit_param'])) {
            throw new AlchemistRestfulApiException("Missing `offset_param` configuration for request_params in `ResourceOffsetPaginator` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceOffsetPaginator::class => [
                    'request_params' => [
                        'limit_param' => 'limit',
                        'offset_param' => 'offset',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);
        }

        if (! isset($componentConfig[ResourceOffsetPaginator::class]['request_params']['offset_param'])) {
            throw new AlchemistRestfulApiException("Missing `limit_param` configuration for request_params in `ResourceOffsetPaginator` component configuration!
                Here is an example of the correct configuration of componentConfigs in your Adapter (inheritance of AlchemistAdapter):

                ```
                ResourceOffsetPaginator::class => [
                    'request_params' => [
                        'limit_param' => 'limit',
                        'offset_param' => 'offset',
                    ]
                ],
                ```
            ", AlchemistRestfulApiException::RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT);
        }

        $limitParam  = $componentConfig[ResourceOffsetPaginator::class]['request_params']['limit_param'];
        $offsetParam = $componentConfig[ResourceOffsetPaginator::class]['request_params']['offset_param'];

        $this->resourceOffsetPaginator = new ResourceOffsetPaginator($limitParam, $offsetParam, [
            $limitParam  => $requestInput[$limitParam] ?? null,
            $offsetParam => $requestInput[$offsetParam] ?? null,
        ]);
    }

    /**
     * @return ResourceOffsetPaginator
     */
    public function resourceOffsetPaginator(): ResourceOffsetPaginator
    {
        return $this->resourceOffsetPaginator;
    }
}