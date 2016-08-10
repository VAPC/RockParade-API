<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\FormattedDateTrait;
use JMS\Serializer\Annotation\Accessor;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * @ORM\Table(name="events", uniqueConstraints={@ORM\UniqueConstraint(name="unique_events_date_name", columns={"date", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 * @author Vehsamrak
 */
class Event
{

    use FormattedDateTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     * @Accessor(getter="getDate")
     * @SerializerType("string")
     */
    protected $date;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    protected $description;
}
