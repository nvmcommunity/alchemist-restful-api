<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Notification;

use Closure;

class ErrorBag
{
    /**
     * @var Closure|null
     */
    private ?Closure $errorHandler = null;

    /**
     * @var bool
     */
    private bool $passes;
    /**
     * @var CompoundErrors
     */
    private CompoundErrors $errors;

    public function __construct(bool $passes, CompoundErrors $errors)
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
     * @param Closure|null $closure
     * @return void
     */
    public function setErrorHandler(?Closure $closure): void
    {
        $this->errorHandler = $closure;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errorHandler ? call_user_func($this->errorHandler, $this->errors) : $this->errors;
    }
}