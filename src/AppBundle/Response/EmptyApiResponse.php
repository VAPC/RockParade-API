<?php

namespace AppBundle\Response;

/**
 * Nullobject for API response. Returns only HTTP code with empty body
 * @author Vehsamrak
 */
class EmptyApiResponse extends ApiResponse
{
    /**
     * @param int $httpCode
     */
    public function __construct(int $httpCode)
    {
        parent::__construct('', $httpCode);
    }
}
