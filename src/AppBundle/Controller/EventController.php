<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Response\ApiError;
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

    /**
     * View event by id
     * @Route("/{eventId}", name="event_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Event",
     *     statusCodes={
     *         200="Event was found",
     *         404="Event with given id was not found",
     *     }
     * )
     * @param string $eventId event id
     */
    public function viewAction(string $eventId): Response
    {
        $eventRepository = $this->get('rockparade.event_repository');
        $event = $eventRepository->findOneById($eventId);

        if ($event) {
            $response = new ApiResponse($event, Response::HTTP_OK);
        } else {
            $response = $this->createEventNotFoundErrorResult($eventId);
        }

        return $this->respond($response);
    }

    private function createEventNotFoundErrorResult(int $eventId): ApiError
    {
        return new ApiError(
            sprintf('Event with id "%s" was not found.', $eventId),
            Response::HTTP_NOT_FOUND
        );
    }
}
