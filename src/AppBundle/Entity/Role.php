<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Type as SerializerType;

/**
 * User role
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoleRepository")
 */
class Role
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @Exclude
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     * @Accessor(getter="getUserLogins")
     * @SerializerType("array")
     */
    private $users;

    /**
     * @param string $name
     * @param string|null $description
     */
    public function __construct($name, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PersistentCollection|User[]
     */
    public function getUsers(): PersistentCollection
    {
        return $this->users;
    }

    /**
     * @return string[]
     */
    public function getUserLogins(): array
    {
        return array_map(function (User $user) {
            return $user->getLogin();
        }, $this->getUsers()->toArray());
    }
}
