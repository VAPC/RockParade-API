<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\Ambassador;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * Organizer
 * @ORM\Table(name="organizers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrganizerRepository")
 */
class Organizer extends Ambassador
{
    /**
     * @var OrganizerMember[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrganizerMember", mappedBy="ambassador", orphanRemoval=true)
     * @Accessor(getter="getMembers")
     * @SerializerType("array")
     */
    protected $members;
}

