<?php

namespace AppBundle\Service\Entity;

use AppBundle\Entity\Event;
use AppBundle\Entity\Link;
use AppBundle\Entity\Repository\EventRepository;
use AppBundle\Entity\Repository\LinkRepository;
use AppBundle\Entity\User;
use AppBundle\Exception\UnsupportedTypeException;
use AppBundle\Form\Event\EventFormType;
use AppBundle\Form\Event\LinksCollectionFormType;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiValidationError;
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
class EventService extends EntityService
{

    /** @var EventRepository */
    private $eventRepository;

    /** @var LinkRepository */
    private $linkRepository;

    /** @var Router */
    private $router;

    /** @var FileService */
    private $fileService;

    public function __construct(
        EventRepository $eventRepository,
        LinkRepository $linkRepository,
        Router $router,
        FileService $fileService
    ) {
        $this->eventRepository = $eventRepository;
        $this->linkRepository = $linkRepository;
        $this->router = $router;
        $this->fileService = $fileService;
    }

    public function createEventByForm(FormInterface $form, User $creator): AbstractApiResponse
    {
        /** @var EventFormType $createEventDTO */
        $createEventDTO = $form->getData();
        $eventDate = new \DateTime($createEventDTO->date);
        $newEvent = new Event(
            $createEventDTO->name,
            $creator,
            $eventDate,
            $createEventDTO->description,
            $createEventDTO->place
        );

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
            $eventDate = new \DateTime($form->get('date')->getData());
            $eventDescription = $form->get('description')->getData();
            $eventPlace = $form->get('place')->getData();

            $event->setName($eventName);
            $event->setDate($eventDate);
            $event->setDescription($eventDescription);
            $event->setPlace($eventPlace);

            try {
                $this->eventRepository->flush();
                $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
            } catch (UniqueConstraintViolationException $exception) {
                $response = new ApiError('Event must have unique name and date.', Response::HTTP_BAD_REQUEST);
            }
        }

        return $response;
    }

    public function addImageToEvent(string $eventId, User $executor, array $imageData = null): AbstractApiResponse
    {
        $event = $this->eventRepository->findOneById($eventId);

        if ($event) {
            if ($executor->getLogin() !== $event->getCreator()->getLogin()) {
            	$response = new ApiError('Only event creator can add images.', Response::HTTP_FORBIDDEN);
            } else {
                $imageName = $imageData['name'] ?: null;
                $imageContent = $imageData['content'] ?? null;

                if (empty($imageData) || !$imageName || !$imageContent) {
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
                                'id'   => $eventId,
                                'imageName' => $image->getName(),
                            ],
                            Router::ABSOLUTE_URL
                        );

                        $response = new LocationApiResponse(Response::HTTP_OK, $imageLocation);
                    } catch (UnsupportedTypeException $exception) {
                        $response = new ApiError(
                            'Only images of types png, gif and jpeg are supported.',
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                }
            }
        } else {
            $response = $this->createEventNotFoundErrorResult($eventId);
        }

        return $response;
    }

    public function addLinksToEvent(string $eventId, User $executor, FormInterface $form): AbstractApiResponse
    {
        if (!$form->isValid()) {
        	return new ApiValidationError($form);
        }

        /** @var Event $event */
        $event = $this->eventRepository->findOneById($eventId);

        if ($event) {
            if ($this->executorIsCreator($executor, $event)) {

                /** @var LinksCollectionFormType $linksCollectionDTO */
                $linksCollectionDTO = $form->getData();

                foreach ($linksCollectionDTO->links as $linkData) {
                    $linkUrl = $linkData['url'] ?? null;
                    $linkDescription = $linkData['description'] ?? null;
                    $link = $this->linkRepository->getOrCreateLink($linkUrl, $linkDescription);
                    $this->linkRepository->persist($link);
                    $event->addLink($link);
                }

                try {
                    $this->eventRepository->flush();
                    $response = new CreatedApiResponse($this->createLocationById($eventId));
                } catch (UniqueConstraintViolationException $exception) {
                    $response = new ApiError('Links must have unique url.', Response::HTTP_BAD_REQUEST);
                }
            } else {
                $response = new ApiError('Only event creator can add links.', Response::HTTP_FORBIDDEN);
            }
        } else {
            $response = $this->createEventNotFoundErrorResult($eventId);
        }

        return $response;
    }

    public function removeLinksFromEvent(string $eventId, string $linkId, User $executor): AbstractApiResponse
    {
        /** @var Event $event */
        $event = $this->eventRepository->findOneById($eventId);

        if ($event) {
            if ($this->executorIsCreator($executor, $event)) {
                /** @var Link $link */
                $link = $this->linkRepository->findOneById($linkId);

                if ($link) {
                    $event->removeLink($link);
                    $this->linkRepository->flush();
                    $response = new EmptyApiResponse(Response::HTTP_OK);
                } else {
                    $response = $this->createLinkNotFoundErrorResult($linkId);
                }
            } else {
                $response = new ApiError('Only event creator can delete links.', Response::HTTP_FORBIDDEN);
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

    private function createLinkNotFoundErrorResult(string $linkUrl): ApiError
    {
        return new ApiError(
            sprintf('Link with url "%s" was not found.', $linkUrl),
            Response::HTTP_NOT_FOUND
        );
    }

    private function executorIsCreator(User $executor, Event $event): bool
    {
        return $executor->getLogin() === $event->getCreator()->getLogin();
    }

    private function createLocationById(string $eventId): string
    {
        return $this->router->generate('event_view', ['id' => $eventId]);
    }
}
