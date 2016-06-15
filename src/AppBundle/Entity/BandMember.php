<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Band member
 * @ORM\Table(name="band_members")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandMemberRepository")
 */
class BandMember
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="login")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Band")
     * @ORM\JoinColumn(name="band_id", referencedColumnName="name")
     */
    private $band;

    /**
     * @var string
     * @ORM\Column(name="short_description", type="text", nullable=false)
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
