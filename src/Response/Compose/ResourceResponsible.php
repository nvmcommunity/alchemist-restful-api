<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Compose;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Response\Compose\Handlers\ResponseCompose;

trait ResourceResponsible
{
    /**
     * @var ResponseCompose
     */
    private ResponseCompose $responseCompose;

    /**
     * @param AlchemistRestfulApi $alchemist
     * @return void
     */
    private function initResponseCompose(AlchemistRestfulApi $alchemist): void
    {
        $this->responseCompose = new ResponseCompose($alchemist);
    }

    /**
     * @return ResponseCompose
     */
    public function responseCompose(): ResponseCompose
    {
        return $this->responseCompose;
    }
}