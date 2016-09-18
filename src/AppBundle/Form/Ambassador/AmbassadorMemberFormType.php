<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Form\AbstractFormType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
abstract class AmbassadorMemberFormType extends AbstractFormType
{

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'login' is mandatory")
     */
    public $login;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'short_description' is mandatory")
     */
    public $shortDescription;

    /** @var string */
    public $description;

    public function setShortDescription(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }
}
