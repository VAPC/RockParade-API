<?php

namespace AppBundle\Response;

use JMS\Serializer\Annotation\Exclude;

/**
 * @author Vehsamrak
 */
class ApiResnonse implements HttpCodeInterface
{
    /** @var array */
    private $data;

    /**
     * @var int
     * @Exclude
     */
    private $httpCode;

    /**
     * @param array $data
     * @param int $httpCode
     */
    public function __construct(array $data, int $httpCode)
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
