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
    private $code;

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        $this->error = $message;
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
