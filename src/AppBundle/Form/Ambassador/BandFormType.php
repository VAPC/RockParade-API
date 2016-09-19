<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
class BandFormType extends AmbassadorFormType
{

    /**
     * @var string
     * @EntityAssert\EntityDoesNotExists(entityClass="AppBundle\Entity\Band", entityField="name")
     */
    public $name;

    public function getEntityClassName(): string
    {
        return Band::class;
    }
}
