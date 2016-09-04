<?php

namespace AppBundle\Response;

/**
 * @author Vehsamrak
 */
class CollectionApiResponse extends ApiResponse
{

    const DEFAULT_LIMIT = 50;

    /** @var int */
    private $total;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    public function __construct($data, int $httpCode, int $total, int $limit = 50, int $offset = 0)
    {
        parent::__construct($data, $httpCode);

        $this->total = $total;
        $this->limit = $limit ?: self::DEFAULT_LIMIT;
        $this->offset = $offset;
    }
}
