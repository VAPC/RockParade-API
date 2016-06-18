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
     * @Assert\NotBlank(message="Parameter 'name' is mandatory")
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'description' is mandatory")
     */
    public $description;

    /**
     * @var string[]
     * @Assert\Count(min=1, minMessage="Parameter 'members' is mandatory")
     */
    public $members;
}
