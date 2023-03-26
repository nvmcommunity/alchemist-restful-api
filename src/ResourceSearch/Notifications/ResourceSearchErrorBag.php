<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Notifications;

class ResourceSearchErrorBag
{
    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;
    /**
     * @var bool
     */
    private bool $invalidDirection;

    /**
     * @param bool $passes
     * @param bool $invalidDirection
     */
    public function __construct(bool $passes, bool $invalidDirection) {
        $this->passes = $passes;
        $this->invalidDirection = $invalidDirection;
    }

    /**
     * @return bool[]
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'invalid_direction' => $this->invalidDirection,
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
     * @return bool
     */
    public function isInvalidDirection(): bool
    {
        return $this->invalidDirection;
    }
}