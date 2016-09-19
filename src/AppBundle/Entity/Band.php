<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\Ambassador;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * Music band (artist, art collective)
 * @ORM\Table(name="bands")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandRepository")
 */
class Band extends Ambassador
{
    /**
     * @var BandMember[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BandMember", mappedBy="ambassador", orphanRemoval=true)
     * @Accessor(getter="getMembers")
     * @SerializerType("array")
     */
    protected $members;
}
