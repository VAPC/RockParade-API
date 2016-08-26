<?php

namespace AppBundle\Service;

/**
 * @author Vehsamrak
 */
class IdGenerator
{

    public static function generateId(): string
    {
        return sprintf('%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
}
