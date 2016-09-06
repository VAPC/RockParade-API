<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\HttpLocationInterface;

/**
 * Empty API response with location.
 * Returns only HTTP code with empty body and location header
 * @author Vehsamrak
 */
class LocationApiResponse extends EmptyApiResponse implements HttpLocationInterface
{

    /** @var string */
    private $location;

    /** {@inheritDoc} */
    public function __construct(int $httpCode, string $location)
    {
        parent::__construct($httpCode);
        $this->location = $location;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
