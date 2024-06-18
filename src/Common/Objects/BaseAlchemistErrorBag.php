<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Objects;

abstract class BaseAlchemistErrorBag
{
    /**
     * @return bool
     */
    abstract public function passes(): bool;

    /**
     * @return string
     */
    abstract public function errorKey(): string;

    /**
     * @return array
     */
    abstract public function getMessages(): array;
}