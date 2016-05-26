<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Organizer
 * @ORM\Table(name="organizers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrganizerRepository")
 */
class Organizer
{

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
     * @ORM\JoinTable(name="users_organizers",
     *      joinColumns={@ORM\JoinColumn(name="organizer_name", referencedColumnName="name")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_login", referencedColumnName="login")}
     *      )
     */
    protected $users;

    /**
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description)
    {
        $this->registrationDate = new \DateTime();
        $this->name = $name;
        $this->description = $description;
        $this->users = new ArrayCollection();
    }
}

