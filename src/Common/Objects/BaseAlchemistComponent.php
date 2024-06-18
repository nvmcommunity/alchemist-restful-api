<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Objects;

abstract class BaseAlchemistComponent
{
    /**
     * @param BaseAlchemistErrorBag|null $notification
     * @return BaseAlchemistErrorBag
     */
    abstract public function validate(&$notification = null): BaseAlchemistErrorBag;
}