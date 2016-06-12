<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\AbstractApiResponse;

/**
 * @author Vehsamrak
 */
class ApiResponse extends AbstractApiResponse
{
    protected $data;
    
    /**
     * @param $data
     */
    public function __construct($data, int $httpCode)
    {
        $this->data = $data;
        $this->httpCode = $httpCode;
    }

    public function getData()
    {
        return $this->data;
    }
}
