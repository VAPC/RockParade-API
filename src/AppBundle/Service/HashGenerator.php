<?php

namespace AppBundle\Service;

/**
 * @author Vehsamrak
 */
class HashGenerator
{

    public static function generate(int $length = 8): string
    {
        $chunksNumber = ceil($length / 4);
        $hashString = '';

        while ($chunksNumber > 0) {
            $chunksNumber--;
            $hashString = sprintf('%s%04x', $hashString, mt_rand(0, 0xffff));
        }

        return substr($hashString, 0, $length);
    }
}
