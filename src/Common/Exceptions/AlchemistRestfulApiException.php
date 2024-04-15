<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions;

use Exception;

class AlchemistRestfulApiException extends Exception
{
    /**
     * This exception is thrown when the configuration of the FieldSelector component is incorrect or missing.
     *
     * You can fix this by adding the correct configuration to the componentConfigs method in your Adapter class.
     * (inheritance of Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter)
     *
     * For example:
     *
     * ```
     * FieldSelector::class => [
     *    'request_params' => [
     *       'fields_param' => 'fields',
     *   ]
     * ],
     * ```
     */
    public const FIELD_SELECTOR_CONFIGURATION_INCORRECT = 356;

    /**
     * This exception is thrown when the configuration of the ResourceFilter component is incorrect or missing.
     *
     * You can fix this by adding the correct configuration to the componentConfigs method in your Adapter class.
     * (inheritance of Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter)
     *
     * For example:
     *
     * ```
     * ResourceFilter::class => [
     *    'request_params' => [
     *       'filter_param' => 'filter',
     *   ]
     * ],
     * ```
     */
    public const RESOURCE_FILTER_CONFIGURATION_INCORRECT = 934;

    /**
     * This exception is thrown when the configuration of the ResourceOffsetPaginator component is incorrect or missing.
     * (inheritance of Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter)
     *
     * You can fix this by adding the correct configuration to the componentConfigs method in your Adapter class.
     * For example:
     *
     * ```
     * ResourceOffsetPaginator::class => [
     *    'request_params' => [
     *       'offset_param' => 'offset',
     *       'limit_param' => 'limit',
     *   ]
     * ],
     * ```
     */
    public const RESOURCE_OFFSET_PAGINATOR_CONFIGURATION_INCORRECT = 768;

    /**
     * This exception is thrown when the configuration of the ResourceSort component is incorrect or missing.
     *
     * You can fix this by adding the correct configuration to the componentConfigs method in your Adapter class.
     * (inheritance of Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter)
     *
     * For example:
     *
     * ```
     * ResourceSort::class => [
     *    'request_params' => [
     *       'sort_param' => 'sort',
     *   ]
     * ],
     * ```
     */
    public const RESOURCE_SORT_CONFIGURATION_INCORRECT = 824;

    /**
     * This exception is thrown when the configuration of the ResourceSearch component is incorrect or missing.
     *
     * You can fix this by adding the correct configuration to the componentConfigs method in your Adapter class.
     * (inheritance of Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter)
     *
     * For example:
     *
     * ```
     * ResourceSearch::class => [
     *    'request_params' => [
     *       'search_param' => 'search',
     *   ]
     * ],
     * ```
     */
    public const RESOURCE_SEARCH_CONFIGURATION_INCORRECT = 198;
}