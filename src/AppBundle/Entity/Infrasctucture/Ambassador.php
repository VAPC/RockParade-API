<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\OrganizerMember;
use AppBundle\Entity\User;
use AppBundle\Exception\UnsupportedEntityException;
use AppBundle\Service\HashGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Accessor;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * @author Vehsamrak
 * @ORM\MappedSuperclass
 */
abstract class Ambassador
{
    use CreatorLoginTrait;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=8, unique=true)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime")
     */
    protected $registrationDate;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /** @var ArrayCollection */
    protected $members;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdBands")
     * @ORM\JoinColumn(name="creator", referencedColumnName="login")
     * @Accessor(getter="getCreatorLogin")
     * @SerializerType("string")
     */
    protected $creator;

    public function __construct(
        string $name,
        User $creator,
        string $description = null,
        HashGenerator $hashGenerator = null
    )
    {
        /** @var HashGenerator $hashGenerator */
        $hashGenerator = $hashGenerator ?: new HashGenerator();
        $this->id = $hashGenerator->generate();
        $this->registrationDate = new \DateTime();
        $this->name = $name;
        $this->description = $description;
        $this->members = new ArrayCollection();
        $this->creator = $creator;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Collection|AmbassadorMember[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(AmbassadorMember $ambassadorMember)
    {
        $this->members->add($ambassadorMember);
    }

    public function removeMember(AmbassadorMember $ambassadorMember)
    {
        $this->members->removeElement($ambassadorMember);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function getMemberClass(): string
    {
        if ($this instanceof Band) {
            return BandMember::class;
        } elseif ($this instanceof Organizer) {
            return OrganizerMember::class;
        } else {
            throw new UnsupportedEntityException();
        }
    }
}
