<?php

namespace AppBundle\Response;

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
