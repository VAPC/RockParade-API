<?php

namespace AppBundle\Entity;

use AppBundle\Service\HashGenerator;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * @ORM\Table(name="events", uniqueConstraints={@ORM\UniqueConstraint(name="unique_events_date_name", columns={"date", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 * @author Vehsamrak
 */
class Event
{

    const DEFAULT_CREATOR = 'creator unknown';

    /**
     * @var int
     * @ORM\Column(name="id", type="string", length=8)
     * @ORM\Id
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     * @Accessor(getter="getDate")
     * @SerializerType("string")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="creator", referencedColumnName="login")
     * @Accessor(getter="getCreatorLogin")
     * @SerializerType("string")
     */
    private $creator;

    public function __construct(
        string $name,
        User $creator,
        \DateTime $date,
        string $description
    )
    {
        $this->name = $name;
        $this->creator = $creator;
        $this->date = $date;
        $this->description = $description;
        $this->id = HashGenerator::generate();
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getCreatorLogin(): string
    {
        return $this->creator ? $this->creator->getLogin() : self::DEFAULT_CREATOR;
    }
}
