<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Notifications;

use Nvmcommunity\Alchemist\RestfulApi\Common\Objects\BaseAlchemistErrorBag;

class ResourceSearchErrorBag extends BaseAlchemistErrorBag
{
    public const INVALID_INPUT_TYPE = 'INVALID_INPUT_TYPE';

    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;

    /**
     * The input type is invalid.
     *
     * @var bool
     */
    private bool $invalidInputType;

    /**
     * @param bool $passes
     * @param bool $invalidInputType
     */
    public function __construct(bool $passes, bool $invalidInputType = false) {
        $this->passes = $passes;
        $this->invalidInputType = $invalidInputType;
    }

    /**
     * @return bool[]
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'invalid_input_type' => $this->invalidInputType,
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
     * @return string
     */
    public function errorKey(): string
    {
        return 'search';
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
                'error_message' => "The input type is invalid. It must be a string.",
            ];
        }

        return $messages;
    }

    /**
     * @return bool
     */
    public function isInvalidInputType(): bool
    {
        return $this->invalidInputType;
    }
}