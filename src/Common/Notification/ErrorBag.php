<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Notification;

use stdClass;

class ErrorBag
{
    /**
     * @var bool
     */
    private bool $passes;
    /**
     * @var stdClass
     */
    private stdClass $errors;

    public function __construct(bool $passes, stdClass $errors)
    {
        $this->passes = $passes;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return $this->passes;
    }

    /**
     * @return stdClass
     */
    public function getErrors(): stdClass
    {
        return $this->errors;
    }
}