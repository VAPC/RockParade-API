<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\GetUserLoginTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Band member
 * @ORM\Table(name="band_members")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandMemberRepository")
 */
class BandMember
{
    use GetUserLoginTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="login")
     * @Serializer\Accessor("getUserLogin")
     * @Serializer\Type("string")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Band")
     * @ORM\JoinColumn(name="band_id", referencedColumnName="name")
     * @Serializer\Exclude()
     */
    private $band;

    /**
     * @var string
     * @ORM\Column(name="short_description", type="text", nullable=false)
     * @Serializer\SerializedName("short_description")
     */
    private $shortDescription;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    public function __construct(User $user, Band $band, string $shortDescription, string $description = '')
    {
        $this->user = $user;
        $this->band = $band;
        $this->shortDescription = $shortDescription;
        $this->description = $description;
    }
}
