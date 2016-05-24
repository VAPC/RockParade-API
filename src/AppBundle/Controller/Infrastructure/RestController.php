<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Response\HttpCodeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class RestController extends Controller
{

    const FORMAT_JSON = 'json';

    /**
     * @param array|object $data
     * @return Response
     */
    public function respond($data): Response
    {
        if ($data) {
            $serializer = $this->get('jms_serializer');
            $serializedData = $serializer->serialize($data, self::FORMAT_JSON);

            $code = Response::HTTP_OK;
            
            if ($data instanceof HttpCodeInterface) {
            	$code = $data->getCode();
            }
            
            $response = new Response($serializedData, $code);
        } else {
            $response = $this->createNotFoundResponse();
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * @return Response
     */
    private function createNotFoundResponse(): Response
    {
        return new Response(json_encode(Response::$statusTexts[Response::HTTP_NOT_FOUND]), Response::HTTP_NOT_FOUND);
    }
}
