<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\CreatorLoginTrait;
use AppBundle\Entity\Infrasctucture\FormattedRegistrationDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation\Accessor;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * Band
 * @ORM\Table(name="bands")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandRepository")
 * @UniqueEntity("name", message="This name is already used. Parameter 'name' must be unique")
 */
class Band
{
    use FormattedRegistrationDateTrait;
    use CreatorLoginTrait;
    
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime")
     * @Accessor(getter="getRegistrationDate")
     * @SerializerType("string")
     */
    private $registrationDate;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var BandMember[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BandMember", mappedBy="band", orphanRemoval=true)
     * @Accessor(getter="getMembers")
     * @SerializerType("array")
     */
    private $members;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdBands")
     * @ORM\JoinColumn(name="creator", referencedColumnName="login")
     * @Accessor(getter="getCreatorLogin")
     * @SerializerType("string")
     */
    private $creator;

    public function __construct(string $name, User $creator, string $description = null)
    {
        $this->registrationDate = new \DateTime();
        $this->name = $name;
        $this->description = $description;
        $this->members = new ArrayCollection();
        $this->creator = $creator;
    }

    /**
     * @return Collection|BandMember[]|ArrayCollection|PersistentCollection
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
