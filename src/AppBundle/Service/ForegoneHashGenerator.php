<?php

namespace AppBundle\Service;

/**
 * @author Vehsamrak
 */
class ForegoneHashGenerator extends HashGenerator
{

    /** @var string */
    private static $hash;

    public function __construct(string $hash)
    {
        self::$hash = $hash;
    }

    public static function generate(int $length = 8): string
    {
        return self::$hash;
    }

}
