<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Vehsamrak
 */
class ApiError extends AbstractApiResponse
{

    /** @var string[] */
    private $errors;

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
}
