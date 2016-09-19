<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Vehsamrak
 * @ORM\MappedSuperclass
 */
abstract class AmbassadorMember
{

    use GetUserLoginTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="login")
     * @Serializer\Accessor("getUserLogin")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("login")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="short_description", type="text", nullable=false)
     * @Serializer\SerializedName("short_description")
     */
    protected $shortDescription;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Ambassador
     */
    protected $ambassador;

    public function __construct(
        Ambassador $ambassador,
        User $user,
        string $shortDescription = '',
        string $description = null
    ) {
        $this->user = $user;
        $this->ambassador = $ambassador;
        $this->shortDescription = $shortDescription;
        $this->description = $description;
    }

    public function setShortDescription(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
