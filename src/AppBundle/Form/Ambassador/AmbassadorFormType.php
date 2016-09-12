<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Form\AbstractFormType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class AmbassadorFormType extends AbstractFormType
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
     */
    public $members;
}
