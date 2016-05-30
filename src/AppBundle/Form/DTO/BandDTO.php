<?php

namespace AppBundle\Form\DTO;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
class BandDTO
{
    /** @var string */
    public $name;
    /** @var User[] */
    public $users;
    /** @var string */
    public $description;
}
