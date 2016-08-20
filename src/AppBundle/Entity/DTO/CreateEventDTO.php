<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class CreateEventDTO
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: name.")
     */
    public $name;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Parameter is mandatory: date.")
     */
    public $date;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: description.")
     */
    public $description;
}
