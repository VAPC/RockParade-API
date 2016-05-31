<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\GetUserLoginsTrait;
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
    use GetUserLoginsTrait;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
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

    /**
     * @var User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="users_bands",
     *      joinColumns={@ORM\JoinColumn(name="band_name", referencedColumnName="name")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_login", referencedColumnName="login")}
     *      )
     * @Accessor(getter="getUserLogins")
     * @SerializerType("array")
     */
    protected $users;

    public function __construct()
    {
        $this->registrationDate = new \DateTime();
        $this->users = new ArrayCollection();
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    /**
     * @return Collection|User[]|ArrayCollection|PersistentCollection
     */
    public function getUsers(): Collection
    {
        return $this->users;
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
}
