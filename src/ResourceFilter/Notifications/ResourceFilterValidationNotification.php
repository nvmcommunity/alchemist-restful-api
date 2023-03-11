<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Notifications;

use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\InvalidFilteringValue;

class ResourceFilterValidationNotification
{
    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;

    /**
     * Missing required filtering
     *
     * @var string[]
     */
    private array $missingRequiredFiltering;

    /**
     * The filtering passed in is invalid.
     *
     * @var string[]
     */
    private array $invalidFiltering;

    /**
     * The filtering passed in contains invalid value.
     *
     * @var InvalidFilteringValue[]
     */
    private array $invalidFilteringValue;

    /**
     * @param bool $passes
     * @param array $missingRequiredFiltering
     * @param array $invalidFiltering
     * @param array $invalidFilteringValue
     */
    public function __construct(
        bool  $passes,
        array $missingRequiredFiltering = [],
        array $invalidFiltering = [],
        array $invalidFilteringValue = []
    ) {
        $this->passes = $passes;
        $this->missingRequiredFiltering = $missingRequiredFiltering;
        $this->invalidFiltering = $invalidFiltering;
        $this->invalidFilteringValue = $invalidFilteringValue;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'missing_required_filtering' => $this->missingRequiredFiltering,
            'invalid_filtering' => $this->invalidFiltering,
            'invalid_filtering_value' => $this->invalidFilteringValue,
        ];
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return $this->passes;
    }

    /**
     * @return string[]
     */
    public function getMissingRequiredFiltering(): array
    {
        return $this->missingRequiredFiltering;
    }

    /**
     * @return string[]
     */
    public function getInvalidFiltering(): array
    {
        return $this->invalidFiltering;
    }

    /**
     * @return array
     */
    public function getInvalidFilteringValue(): array
    {
        return $this->invalidFilteringValue;
    }

    /**
     * @return bool
     */
    public function hasMissingRequiredFiltering(): bool
    {
        return ! empty($this->missingRequiredFiltering);
    }

    /**
     * @return bool
     */
    public function hasInvalidFiltering(): bool
    {
        return ! empty($this->invalidFiltering);
    }

    /**
     * @return bool
     */
    public function hasInvalidFilteringValue(): bool
    {
        return ! empty($this->invalidFilteringValue);
    }
}