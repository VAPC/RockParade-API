<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Exclude;

/**
 * @author Vehsamrak
 */
class ApiError implements HttpCodeInterface
{

    /** @var string */
    private $error;

    /**
     * @var int
     * @Exclude
     */
    private $httpCode;

    /**
     * @param string $message
     * @param int $httpCode
     */
    public function __construct(string $message, int $httpCode)
    {
        $this->error = $message;
        $this->httpCode = $httpCode;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
