<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\AmbassadorMember;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Band member
 * @ORM\Table(name="band_members")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandMemberRepository")
 */
class BandMember extends AmbassadorMember
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Band", inversedBy="members")
     * @ORM\JoinColumn(name="band_id", referencedColumnName="id")
     * @Serializer\Exclude
     * @var Band
     */
    protected $ambassador;
}
