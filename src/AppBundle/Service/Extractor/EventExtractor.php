<?php

namespace AppBundle\Service\Extractor;

use AppBundle\Entity\Event;
use AppBundle\Entity\Image;
use AppBundle\Entity\Link;
use AppBundle\Exception\UnsupportedEntityException;
use AppBundle\Service\Extractor\Infrastructure\AbstractExtractor;

/**
 * @author Vehsamrak
 */
class EventExtractor extends AbstractExtractor
{

    /**
     * @param Event $entity
     * @throws UnsupportedEntityException
     */
    public function extract($entity): array
    {
        if ($entity instanceof Event) {
            $serializedData = [
                'id'          => $entity->getId(),
                'date'        => $entity->getDate(),
                'name'        => $entity->getName(),
                'description' => $entity->getDescription(),
                'creator'     => $entity->getCreatorLogin(),
                'images'      => $this->getImages($entity),
                'links'       => $this->getLinks($entity),
            ];
        } else {
            throw new UnsupportedEntityException();
        }

        return $serializedData;
    }

    /**
     * @return string[]
     */
    private function getImages(Event $event): array
    {
        $images = $event->getImages();
        $eventId = $event->getId();

        return array_map(
            function (Image $image) use ($eventId) {
                return $this->generateImageUrl($eventId, $image->getName());
            },
            $images->toArray()
        );
    }

    private function getLinks(Event $event): array
    {
        return array_map(
            function (Link $link) {
                return [
                    'id'          => $link->getId(),
                    'url'         => $link->getUrl(),
                    'description' => $link->getDescription(),
                ];
            },
            $event->getLinks()->toArray()
        );
    }

    private function generateImageUrl(string $eventId, string $imageName): string
    {
        return $this->generateUrl(
            'event_image_view',
            [
                'eventId'   => $eventId,
                'imageName' => $imageName,
            ]
        );
    }
}
