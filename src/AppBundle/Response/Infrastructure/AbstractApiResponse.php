<?php

namespace AppBundle\Response\Infrastructure;

use JMS\Serializer\Annotation\Exclude;

/**
 * @author Vehsamrak
 */
abstract class AbstractApiResponse implements HttpCodeInterface
{
    /**
     * @var int
     * @Exclude
     */
    protected $httpCode;


    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
