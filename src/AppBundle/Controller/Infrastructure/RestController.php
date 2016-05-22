<?php

namespace AppBundle\Controller\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class RestController extends Controller
{

    const FORMAT_JSON = 'json';

    /**
     * @param array $data
     * @return mixed|string
     */
    public function respond(array $data): Response
    {
        $serializer = $this->get('jms_serializer');
        $serializedData = $serializer->serialize($data, self::FORMAT_JSON);
        
        $response = new Response($serializedData);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
