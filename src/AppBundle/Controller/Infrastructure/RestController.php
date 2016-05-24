<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Response\ApiResnonse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class RestController extends Controller
{

    const FORMAT_JSON = 'json';

    /**
     * @param ApiResnonse $responseContents
     * @return Response
     */
    public function respond(ApiResnonse $responseContents): Response
    {
        $serializer = $this->get('jms_serializer');
        $serializedData = $serializer->serialize($responseContents, self::FORMAT_JSON);

        $response = new Response($serializedData, $responseContents->getHttpCode());
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
}
