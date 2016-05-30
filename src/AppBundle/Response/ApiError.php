<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Exclude;

/**
 * @author Vehsamrak
 */
class ApiError implements HttpCodeInterface
{

    /** @var string[] */
    private $errors;

    /**
     * @var int
     * @Exclude
     */
    private $httpCode;

    /**
     * @param string|string[] $errors
     * @param int $httpCode
     */
    public function __construct($errors, int $httpCode)
    {
        $this->errors = (array) $errors;
        $this->httpCode = $httpCode;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
