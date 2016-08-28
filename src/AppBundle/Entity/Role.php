<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\GetUserLoginTrait;
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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RoleRepository")
 */
class Role
{
    
    use GetUserLoginTrait;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
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
}
