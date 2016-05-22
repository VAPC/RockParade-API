<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="login", type="string", length=255, nullable=false, unique=true)
     */
    private $login;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime", nullable=false)
     */
    private $registrationDate;

    /**
     * @var Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(
     *     name="user_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="login")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="name")}
     *     )
     */
    private $roles;

    /**
     * @param string $login
     * @param string $name
     * @param string|null $description
     */
    public function __construct(string $login, string $name, string $description = null)
    {
        $this->login = $login;
        $this->name = $name;
        $this->description = $description;
        $this->registrationDate = new \DateTime();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Role[]|ArrayCollection
     */
    public function getRoles(): ArrayCollection
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }
}
