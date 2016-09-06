<?php

namespace AppBundle\Service;

use AppBundle\Entity\DTO\CreateEventDTO;
use AppBundle\Entity\Event;
use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Entity\User;
use AppBundle\Exception\UnsupportedTypeException;
use AppBundle\Response\ApiError;
use AppBundle\Response\CreatedApiResponse;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Response\LocationApiResponse;
use AppBundle\Service\File\FileService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

/**
 * @author Vehsamrak
 */
class EventService
{

    /** @var EventRepository */
    private $eventRepository;

    /** @var Router */
    private $router;

    /** @var FileService */
    private $fileService;

    public function __construct(
        EventRepository $eventRepository,
        Router $router,
        FileService $fileService
    ) {
        $this->eventRepository = $eventRepository;
        $this->router = $router;
        $this->fileService = $fileService;
    }

    public function createEventByForm(FormInterface $form, User $creator): AbstractApiResponse
    {
        /** @var CreateEventDTO $createEventDTO */
        $createEventDTO = $form->getData();

        $newEvent = new Event($createEventDTO->name, $creator, $createEventDTO->date, $createEventDTO->description);

        $this->eventRepository->persist($newEvent);

        try {
            $this->eventRepository->flush();
            $response = new CreatedApiResponse($this->createLocationById($newEvent->getId()));
        } catch (UniqueConstraintViolationException $exception) {
            $response = new ApiError('Event must have unique name and date.', Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    public function editEventByForm(FormInterface $form, string $eventId): AbstractApiResponse
    {
        /** @var Event $event */
        $event = $this->eventRepository->findOneById($eventId);

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
                $this->eventRepository->flush();
                $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
            } catch (UniqueConstraintViolationException $exception) {
                $response = new ApiError('Event must have unique name and date.', Response::HTTP_BAD_REQUEST);
            }
        }

        return $response;
    }

    public function addImageToEvent(string $eventId, array $imageData = null): AbstractApiResponse
    {
        $event = $this->eventRepository->findOneById($eventId);

        if ($event) {
            $imageName = $imageData['name'] ?: null;
            $imageContent = $imageData['content'] ?? null;

            if (!$imageData || !$imageName || !$imageContent) {
                $response = new ApiError(
                    'Parameters are mandatory: image[name] and image[content].',
                    Response::HTTP_BAD_REQUEST
                );
            } else {
                try {
                    $imageExtension = $this->fileService->getExtensionFromBase64File($imageContent);
                    $imageName = sprintf('%s.%s', $imageName, $imageExtension);
                    $image = $this->fileService->createBase64Image($imageName, $imageContent, $event);

                    $imageLocation = $this->router->generate(
                        'event_image_view',
                        [
                            'eventId'   => $eventId,
                            'imageName' => $image->getName(),
                        ]
                    );

                    $response = new LocationApiResponse(Response::HTTP_OK, $imageLocation);
                } catch (UnsupportedTypeException $exception) {
                    $response = new ApiError(
                        'Only images of types png, gif and jpeg are supported.',
                        Response::HTTP_BAD_REQUEST
                    );
                }
            }
        } else {
            $response = $this->createEventNotFoundErrorResult($eventId);
        }

        return $response;
    }

    public function createEventNotFoundErrorResult(string $eventId): ApiError
    {
        return new ApiError(
            sprintf('Event with id "%s" was not found.', $eventId),
            Response::HTTP_NOT_FOUND
        );
    }

    private function createLocationById(string $eventId): string
    {
        return $this->router->generate('event_view', ['eventId' => $eventId]);
    }
}
