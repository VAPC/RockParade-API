<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Vehsamrak
 */
class HttpDomainException extends HttpException
{

    const HTTP_ERROR_CODE = 500;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(self::HTTP_ERROR_CODE, $message);
    }
}
