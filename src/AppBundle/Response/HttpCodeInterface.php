<?php

namespace AppBundle\Response;

/**
 * @author Vehsamrak
 */
interface HttpCodeInterface
{

    /** @return int */
    public function getCode(): int;
}
