<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\DTO\CreateEventDTO;
use AppBundle\Entity\Event;
use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Response\ApiError;
use AppBundle\Response\CreatedApiResponse;
use AppBundle\Response\EmptyApiResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
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
     * @Route("s/{limit}/{offset}", name="events_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Event",
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     * @param int $limit Limit results. Default is 50
     * @param int $offset Starting serial number of result collection. Default is 0
     */
    public function listAction($limit = null, $offset = null): Response
    {
        return $this->respond(
            $this->createCollectionResponse(
                $this->get('rockparade.event_repository'),
                $limit,
                $offset
            )
        );
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
        /** @var EventRepository $eventRepository */
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
     * Create new event
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
     *             "dataType"="date (dd-MM-yyyy HH:mm)",
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
     * @Security("has_role('ROLE_USER')")
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createEventCreationForm();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $newEvent = $this->createEventByForm($form);

            /** @var EventRepository $eventRepository */
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

    /**
     * Edit event
     * @Route("/{eventId}", name="event_edit")
     * @Method("PUT")
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
     *             "dataType"="date (dd-MM-yyyy HH:mm)",
     *             "requirement"="true",
     *             "description"="event date"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="event description"
     *         },
     *     },
     *     statusCodes={
     *         204="Event was edited with new data",
     *         400="Validation error",
     *         404="Event with given id was not found",
     *     }
     * )
     * @param string $eventId event id
     */
    public function editAction(Request $request, string $eventId): Response
    {
        $form = $this->createEventCreationForm();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            /** @var EventRepository $eventRepository */
            $eventRepository = $this->get('rockparade.event_repository');
            /** @var Event $event */
            $event = $eventRepository->findOneById($eventId);

            if (!$event) {
                $response = $this->createEventNotFoundErrorResult($eventId);
            } else {
                $eventName = $form->get('name')->getData();
                /** @var \DateTime $eventDate */
                $eventDate = $form->get('date')->getData();
                $eventDescription = $form->get('description')->getData();

                $event->setName($eventName);
                $event->setDate($eventDate);
                $event->setDescription($eventDescription);

                try {
                    $eventRepository->flush();
                    $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
                } catch (UniqueConstraintViolationException $exception) {
                    $response = new ApiError(['Event must have unique name and date.'], Response::HTTP_BAD_REQUEST);
                }
            }

        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }

        return $this->respond($response);
    }

    /**
     * Delete event
     * @Route("/{eventId}", name="event_delete")
     * @Method("DELETE")
     * @ApiDoc(
     *     section="Event",
     *     statusCodes={
     *         204="Event was deleted",
     *         404="Event with given id was not found",
     *     }
     * )
     * @param string $eventId event id
     */
    public function deleteEvent(string $eventId): Response
    {
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->get('rockparade.event_repository');
        $event = $eventRepository->findOneById($eventId);

        if ($event) {
            $eventRepository->remove($event);
            $eventRepository->flush();

            $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
        } else {
            $response = $this->createEventNotFoundErrorResult($eventId);
        }


        return $this->respond($response);
    }

    private function createEventNotFoundErrorResult(string $eventId): ApiError
    {
        return new ApiError(
            sprintf('Event with id "%s" was not found.', $eventId),
            Response::HTTP_NOT_FOUND
        );
    }

    private function createEventCreationForm(): FormInterface
    {
        /** @var FormBuilder $formBuilder */
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

    private function createLocationById(string $eventId): string
    {
        return $this->generateUrl('event_view', ['eventId' => $eventId]);
    }

    private function createEventByForm(FormInterface $form): Event
    {
        /** @var CreateEventDTO $createEventDTO */
        $createEventDTO = $form->getData();
        $creator = $this->getUser();

        return new Event($createEventDTO->name, $creator, $createEventDTO->date, $createEventDTO->description);
    }
}
