<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response;

use Nvmcommunity\Alchemist\RestfulApi\Response\Exceptions\Contracts\ApplicationExceptionInterface;
use Nvmcommunity\Alchemist\RestfulApi\Response\Objects\Contracts\ActionMessage;
use Nvmcommunity\Alchemist\RestfulApi\Response\Objects\Contracts\ResourceCollection;
use Nvmcommunity\Alchemist\RestfulApi\Response\Objects\Contracts\ResourceDetail;
use Psr\Http\Message\ResponseInterface;

interface ResourceResponseInterface
{
    /**
     * @param ResourceDetail $resourceDetail
     * @return ResponseInterface
     */
    public function resourceGetDetail(ResourceDetail $resourceDetail): ResponseInterface;

    /**
     * @param ResourceCollection $resourceCollection
     * @return ResponseInterface
     */
    public function resourceGetCollection(ResourceCollection $resourceCollection): ResponseInterface;

    /**
     * @param ActionMessage|null $actionMessage
     * @return ResponseInterface
     */
    public function resourceDelete(?ActionMessage $actionMessage = null): ResponseInterface;

    /**
     * @param ActionMessage|null $actionMessage
     * @return ResponseInterface
     */
    public function resourceUpdate(?ActionMessage $actionMessage = null): ResponseInterface;

    /**
     * @param ActionMessage|null $actionMessage
     * @return ResponseInterface
     */
    public function resourceCreate(?ActionMessage $actionMessage = null): ResponseInterface;

    /**
     * @param ApplicationExceptionInterface $exception
     * @return ResponseInterface
     */
    public function resourceException(ApplicationExceptionInterface $exception): ResponseInterface;
}