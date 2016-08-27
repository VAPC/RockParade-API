<?php

namespace AppBundle\Service;

use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class HttpResponseFactory
{
    const FORMAT_JSON = 'json';

    /** @var Serializer */
    private $serializer;

    public function __construct(Serializer $serializer) {
        $this->serializer = $serializer;
    }

    public function createResponse(AbstractApiResponse $apiResponse): Response
    {
        $serializedData = $this->serialize($apiResponse);
        $response = new Response($serializedData, $apiResponse->getHttpCode());

        return $response;
    }

    private function serialize(AbstractApiResponse $apiResponse): string
    {
        if (!$apiResponse instanceof EmptyApiResponse) {
            $serializedData = $this->serializer->serialize($apiResponse, self::FORMAT_JSON);
        }

        return $serializedData ?? '';
    }
}
