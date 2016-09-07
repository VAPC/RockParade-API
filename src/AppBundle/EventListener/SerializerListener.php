<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Event;
use AppBundle\Exception\UnsupportedEntityException;
use AppBundle\Service\Extractor\Extractor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * @author Vehsamrak
 */
class SerializerListener implements SubscribingHandlerInterface
{

    /** @var Extractor */
    private $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /** {@inheritDoc} */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => Event::class,
                'method'    => 'extract',
            ],
        ];
    }

    /**
     * @param object $entity Entity object
     * @throws UnsupportedEntityException
     */
    public function extract(
        JsonSerializationVisitor $visitor,
        $entity
    ): array
    {
        return $this->extractor->extract($entity);
    }
}
