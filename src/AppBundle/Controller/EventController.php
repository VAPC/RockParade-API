<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Response\ApiResponse;

/**
 * @Route("event")
 * @author Vehsamrak
 */
class EventController extends RestController
{
    /**
     * List all events
     * @Route("s/", name="events_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Event",
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     */
    public function listAction(): Response
    {
        $eventRepository = $this->get('rockparade.event_repository');
        $response = new ApiResponse($eventRepository->findAll(), Response::HTTP_OK);

        return $this->respond($response);
    }
}
