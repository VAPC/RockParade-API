<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\BandMember;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Accessor;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * @author Vehsamrak
 * @ORM\MappedSuperclass()
 */
abstract class Ambassador
{
    use FormattedRegistrationDateTrait;
    use CreatorLoginTrait;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime")
     * @Accessor(getter="getRegistrationDate")
     * @SerializerType("string")
     */
    protected $registrationDate;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var BandMember[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BandMember", mappedBy="band", orphanRemoval=true)
     * @Accessor(getter="getMembers")
     * @SerializerType("array")
     */
    protected $members;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdBands")
     * @ORM\JoinColumn(name="creator", referencedColumnName="login")
     * @Accessor(getter="getCreatorLogin")
     * @SerializerType("string")
     */
    protected $creator;

    public function __construct(string $name, User $creator, string $description = null)
    {
        $this->registrationDate = new \DateTime();
        $this->name = $name;
        $this->description = $description;
        $this->members = new ArrayCollection();
        $this->creator = $creator;
    }

    /**
     * @return Collection|BandMember[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(BandMember $bandMember)
    {
        $this->members->add($bandMember);
    }

    public function removeMember(BandMember $bandMember)
    {
        $this->members->removeElement($bandMember);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
