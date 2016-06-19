<?php

namespace AppBundle\Entity;

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
     * @Accessor(getter="getUserLogins")
     * @SerializerType("array")
     */
    protected $members;

    /**
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description = null)
    {
        $this->registrationDate = new \DateTime();
        $this->name = $name;
        $this->description = $description;
        $this->members = new ArrayCollection();
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string[]
     */
    public function getUserLogins(): array
    {
        return array_map(function (BandMember $bandMember) {
            return $bandMember->getUser()->getLogin();
        },
            $this->members->toArray()
        );
    }
}
