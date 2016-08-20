<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\DTO\CreateEventDTO;
use AppBundle\Entity\Event;
use AppBundle\Response\ApiError;
use AppBundle\Response\CreatedApiResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("")
     * @Method("POST")
     * @ApiDoc(
     *     section="Event",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="event name"
     *         },
     *         {
     *             "name"="date",
     *             "dataType"="date (yyyy-MM-dd HH:mm)",
     *             "requirement"="true",
     *             "description"="event date"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="text",
     *             "requirement"="true",
     *             "description"="event description"
     *         },
     *     },
     *     statusCodes={
     *         201="New event was created. Link to new resource in header 'Location'",
     *         400="Validation error",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createEventCreationForm();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $newEvent = $this->createEventByForm($form);
            $eventRepository = $this->get('rockparade.event_repository');

            $eventRepository->persist($newEvent);

            try {
                $eventRepository->flush();
                $response = new CreatedApiResponse($this->createLocationById($newEvent->getId()));
            } catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError('Event must have unique name and date.'));
                $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
            }

        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
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

    private function createEventCreationForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder(new CreateEventDTO());
        $formBuilder->add('name', TextType::class);
        $formBuilder->add(
            'date',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
            ]
        );
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function createLocationById(int $eventId): string
    {
        return $this->generateUrl('event_view', ['eventId' => $eventId]);
    }

    private function createEventByForm(FormInterface $form): Event
    {
        /** @var CreateEventDTO $createEventDTO */
        $createEventDTO = $form->getData();

        return new Event($createEventDTO->name, $createEventDTO->date, $createEventDTO->description);
    }
}
