<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Organizer;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
class OrganizerFormType extends AmbassadorFormType
{

    /**
     * @var string
     * @EntityAssert\EntityDoesNotExists(entityClass="AppBundle\Entity\Organizer", entityField="name")
     */
    public $name;

    public function getEntityClassName(): string
    {
        return Organizer::class;
    }
}
