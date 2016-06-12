<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\AbstractApiResponse;

/**
 * @author Vehsamrak
 */
class ApiResponse extends AbstractApiResponse
{
    /** @var array */
    protected $data;

    /**
     * @param $data
     * @param int $httpCode
     */
    public function __construct($data, int $httpCode)
    {
        $this->data = $data;
        $this->httpCode = $httpCode;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
