<?php

namespace AppBundle\Form\DTO;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
class Band
{
    /** @var string */
    private $name;
    /** @var User[] */
    private $users;
    /** @var string */
    private $description;

    /**
     * @param string $name
     * @param User[] $users
     * @param string $description
     */
    public function __construct(string $name, array $users, string $description)
    {
        $this->name = $name;
        $this->users = $users;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
