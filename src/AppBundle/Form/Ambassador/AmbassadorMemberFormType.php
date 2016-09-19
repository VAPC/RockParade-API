<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Form\AbstractFormType;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
abstract class AmbassadorMemberFormType extends AbstractFormType
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'ambassador' is mandatory.")
     */
    public $ambassador;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'login' is mandatory.")
     * @EntityAssert\EntityExists(entityClass="AppBundle\Entity\User", entityField="login")
     */
    public $login;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'short_description' is mandatory.")
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
