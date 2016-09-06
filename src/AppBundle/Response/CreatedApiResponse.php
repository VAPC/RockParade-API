<?php

namespace AppBundle\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * API response for created resource. Returns 201 HTTP code, location, and empty body
 * @author Vehsamrak
 */
class CreatedApiResponse extends LocationApiResponse
{

    /**
     * @param string $location
     */
    public function __construct(string $location)
    {
        parent::__construct(Response::HTTP_CREATED, $location);
    }
}
