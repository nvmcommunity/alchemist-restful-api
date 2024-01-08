<?php
namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector;

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Exceptions\FieldSelectorSyntaxErrorException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;

trait FieldSelectable
{
    /**
     * @var FieldSelector
     */
    private FieldSelector $fieldSelector;

    /**
     * @param string $fields
     * @return void
     */
    private function initFieldSelector(string $fields): void
    {
        $this->fieldSelector = new FieldSelector($fields);
    }

    /**
     * @return FieldSelector
     */
    public function fieldSelector(): FieldSelector
    {
        return $this->fieldSelector;
    }
}