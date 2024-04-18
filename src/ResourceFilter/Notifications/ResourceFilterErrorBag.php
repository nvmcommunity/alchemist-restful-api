<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Notifications;

use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\InvalidFilteringValue;

class ResourceFilterErrorBag
{
    public const MISSING_REQUIRED_FILTERING = 'MISSING_REQUIRED_FILTERING';
    public const INVALID_FILTERING = 'INVALID_FILTERING';
    public const INVALID_FILTERING_VALUE = 'INVALID_FILTERING_VALUE';
    public const INVALID_INPUT_TYPE = 'INVALID_INPUT_TYPE';
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
     * The input type is invalid.
     *
     * @var bool
     */
    private bool $invalidInputType;

    /**
     * @param bool $passes
     * @param array $missingRequiredFiltering
     * @param array $invalidFiltering
     * @param array $invalidFilteringValue
     * @param bool $invalidInputType
     */
    public function __construct(
        bool  $passes,
        array $missingRequiredFiltering = [],
        array $invalidFiltering = [],
        array $invalidFilteringValue = [],
        bool $invalidInputType = false
    ) {
        $this->passes = $passes;
        $this->missingRequiredFiltering = $missingRequiredFiltering;
        $this->invalidFiltering = $invalidFiltering;
        $this->invalidFilteringValue = $invalidFilteringValue;
        $this->invalidInputType = $invalidInputType;
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
            'invalid_input_type' => $this->invalidInputType,
        ];
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        if ($this->passes()) {
            return [];
        }

        $messages = [];

        if ($this->isInvalidInputType()) {
            $messages[] = [
                'error_code' => static::INVALID_INPUT_TYPE,
                'error_message' => "The input type is invalid. It must be an array.",
            ];
        }

        if ($this->hasInvalidFiltering()) {
            $messages[] = [
                'error_code' => static::INVALID_FILTERING,
                'error_message' => "You have passed in invalid filtering.",
                'error_filtering' => $this->getInvalidFiltering(),
            ];
        }

        if ($this->hasInvalidFilteringValue()) {

            $invalidFilteringValue = array_map(static function($e) {
                $data = [];

                $data[] = $e->getFiltering();

                if (! empty($e->getSupportedFormats())) {
                    $data[] = implode('|', $e->getSupportedFormats());
                } elseif (! empty($e->getSupportedValue())) {
                    $data[] = implode('|', $e->getSupportedValue());
                } elseif (! empty($e->getSupportedType())) {
                    $data[] = implode('|', $e->getSupportedType());
                }

                return implode('^', $data);

            } , $this->getInvalidFilteringValue());

            $messages[] = [
                'error_code' => static::INVALID_FILTERING_VALUE,
                'error_message' => "You have passed in invalid filtering value.",
                'error_filtering' => $invalidFilteringValue,
            ];
        }

        if ($this->hasMissingRequiredFiltering()) {
            $messages[] = [
                'error_code' => static::MISSING_REQUIRED_FILTERING,
                'error_message' => "You have missed required filtering.",
                'error_filtering' => $this->getMissingRequiredFiltering(),
            ];
        }

        return $messages;
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

    /**
     * @return bool
     */
    public function isInvalidInputType(): bool
    {
        return $this->invalidInputType;
    }
}