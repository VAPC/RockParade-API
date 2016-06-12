<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\AbstractApiResponse;

/**
 * @author Vehsamrak
 */
class ApiResponse extends AbstractApiResponse
{
    /**
     * @var string|array
     */
    protected $data;
    
    /**
     * @param string|array $data
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
