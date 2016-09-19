<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Infrasctucture\AmbassadorMember;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Vehsamrak
 * @ORM\Table(name="organizer_members")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Infrasctucture\AmbassadorMemberRepository")
 */
class OrganizerMember extends AmbassadorMember
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organizer", inversedBy="members")
     * @ORM\JoinColumn(name="organizer_id", referencedColumnName="id")
     * @Serializer\Exclude
     * @var Organizer
     */
    protected $ambassador;
}
