<?php

namespace AppBundle\Response\Infrastructure;

/**
 * @author Vehsamrak
 */
interface HttpCodeInterface
{

    /**
     * Get HTTP code
     * @return int
     */
    public function getHttpCode(): int;
}
