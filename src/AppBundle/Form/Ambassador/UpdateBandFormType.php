<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
class UpdateBandFormType extends AmbassadorFormType
{

    public function getEntityClassName(): string
    {
        return Band::class;
    }
}
