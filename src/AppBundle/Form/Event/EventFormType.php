<?php

namespace AppBundle\Form\Event;

use AppBundle\Entity\Event;
use AppBundle\Form\AbstractFormType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class EventFormType extends AbstractFormType
{

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: name.")
     */
    public $name;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Parameter is mandatory: date (yyyy-MM-dd HH:mm).")
     */
    public $date;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: description.")
     */
    public $description;

    public function getEntityClassName(): string
    {
        return Event::class;
    }
}
