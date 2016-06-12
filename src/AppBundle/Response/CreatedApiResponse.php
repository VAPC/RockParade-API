<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\HttpLocationInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * API response for created resource. Returns 201 HTTP code, location, and empty body
 * @author Vehsamrak
 */
class CreatedApiResponse extends EmptyApiResponse implements HttpLocationInterface
{
    private $location;

    /** {@inheritDoc} */
    public function __construct(string $location)
    {
        $this->location = $location;
        parent::__construct(Response::HTTP_CREATED);
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
