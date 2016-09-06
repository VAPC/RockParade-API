<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Event;
use AppBundle\Entity\Image;
use AppBundle\Exception\UnsupportedEntityException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author Vehsamrak
 */
class SerializerListener implements SubscribingHandlerInterface
{

    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /** {@inheritDoc} */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => Event::class,
                'method'    => 'serialize',
            ],
        ];
    }

    /**
     * @param object $entity
     * @throws UnsupportedEntityException
     */
    public function serialize(
        JsonSerializationVisitor $visitor,
        $entity
    ): array
    {
        if ($entity instanceof Event) {

            $serializedData = [
                'id'          => $entity->getId(),
                'date'        => $entity->getDate(),
                'name'        => $entity->getName(),
                'description' => $entity->getDescription(),
            ];

            $creatorLogin = $entity->getCreatorLogin();

            if ($creatorLogin) {
            	$serializedData['creator'] = $creatorLogin;
            }

            $images = $this->getImages($entity);

            if ($images) {
                $serializedData['images'] = $images;
            }
        } else {
            throw new UnsupportedEntityException();
        }

        return $serializedData;

    }

    /**
     * @return string[]|null
     */
    private function getImages(Event $event)
    {
        $images = $event->getImages();

        if ($images) {
            $eventId = $event->getId();

            return array_map(
                function (Image $image) use ($eventId) {
                    return $this->generateImageUrl($eventId, $image->getName());
                },
                $images->toArray()
            );
        } else {
            return null;
        }
    }

    private function generateImageUrl(string $eventId, string $imageName)
    {
        return $this->router->generate(
            'event_image_view',
            [
                'eventId'   => $eventId,
                'imageName' => $imageName,
            ],
            Router::ABSOLUTE_URL
        );
    }
}
