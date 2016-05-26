<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Vehsamrak
 */
class ApiError extends ApiResnonse
{

    /** @var string */
    private $error;

    /**
     * @param string $message
     * @param int $httpCode
     */
    public function __construct(string $message, int $httpCode)
    {
        $this->error = $message;
        parent::__construct($message, $httpCode);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
