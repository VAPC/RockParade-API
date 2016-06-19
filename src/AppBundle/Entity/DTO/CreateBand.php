<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class CreateBand
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: name.")
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: description.")
     */
    public $description;

    /**
     * @var string[]
     * @Assert\Count(min=1, minMessage="Parameter is mandatory: members.")
     */
    public $members;
}
