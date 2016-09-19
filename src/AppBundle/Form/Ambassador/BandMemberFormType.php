<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
class BandMemberFormType extends AmbassadorMemberFormType
{

    /**
     * @var string
     * @EntityAssert\EntityExists(entityClass="AppBundle\Entity\Band", entityField="id")
     */
    public $ambassador;

    public function getEntityClassName(): string
    {
        return Band::class;
    }
}
