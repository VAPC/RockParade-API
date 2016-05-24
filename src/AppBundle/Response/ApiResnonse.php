<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation\Exclude;

/**
 * @author Vehsamrak
 */
class ApiResnonse implements HttpCodeInterface
{
    /** @var array */
    protected $data;

    /**
     * @var int
     * @Exclude
     */
    protected $httpCode;

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

    /** {@inheritDoc} */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
