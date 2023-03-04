<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Helpers;

class Strings extends \Nette\Utils\Strings
{
    /**
     * @param string $haystack
     * @param string $delimiter
     * @return string
     */
    public static function start(string $haystack, string $delimiter): string
    {
        $explode = explode($delimiter, $haystack);

        return array_shift($explode);
    }
}